<?php

namespace App\Http\Controllers;

use App\model\Annonce;
use App\model\Categorie;
use App\model\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnonceController extends Controller
{
    public function index()
    {
        $annonces = \DB::table('annonces')
            ->leftJoin('images', function ($join){
                $join->on('annonces.id', '=', 'images.annonce_id');
            })
            ->join('categories', 'annonces.categorie_id', '=', 'categories.id')
            ->select('annonces.*','images.url','categories.name')
            ->groupBy('annonces.id')
            ->orderBy('annonces.created_at', 'DESC')
            ->get();
        return (response()->json($annonces));
    }

    public function store(Request $request)
    {
        $this->validator($request->all());
        $categorie = Categorie::where(['name'=>$request->name])->firstOrFail();
        $user = auth()->user();
        $annonce = Annonce::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 1,
            'produit_avec_date_expiration' => $request->produit_avec_date_expiration,
            'user_id' => $user->id,
            'categorie_id' => $categorie->id,
            'date_expiration' => $request->date_expiration
        ]);
        Image::create([
            'url' => $request->url,
            'annonce_id' => $annonce->id,
            'className'=> 'annonce'
        ]);
        return response()->json(['message'=>'annonce created successfully'],200);
    }

    public function show($id)
    {
        $annonce = Annonce::findOrFail($id);
        $pictures = $annonce->images()->get();
        $user = $annonce->user()->get();
        $categorie = $annonce->categorie()->get();
        $currentUser = auth()->user();
        return response()->json(['annonce' => $annonce , 'images' => $pictures ,
            'user' => $user , 'categorie' => $categorie , 'currentUser' => $currentUser],200);
    }

    public function update(Request $request)
    {
        $this->validator($request->all());
        $categorie = Categorie::where(['name'=>$request->name])->firstOrFail();
        $user = auth()->user();
        $annonce = Annonce::find($request->id);
        if($user->id == $annonce->user_id){
            $annonce->update([
                'title' => $request->title,
                'description' => $request->description,
                'produit_avec_date_expiration' => $request->produit_avec_date_expiration,
                'categorie_id' => $categorie->id,
                'date_expiration' => $request->date_expiration
            ]);
            $oldImages = Image::where(['annonce_id'=>$annonce->id])->get();
            if($oldImages){
                foreach ($oldImages as $value) {
                    Image::destroy($value->id);
                }
            }
            Image::create([
                'url' => $request->url,
                'annonce_id' => $annonce->id,
                'className'=> 'annonce'
            ]);
            return response()->json(['message'=>'annonce updated successfully'],200);
        }
        return response()->json(['error'=>'Erreur lors du update'],419);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, $this->rules(), $this->messages())->validate();
    }
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:400',
        ];
    }
    /**
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Le champ titre est obligatoire',
            'description.required'  => 'Le champ description est obligatoire',
        ];
    }

}
