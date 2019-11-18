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
            ->where('status',1)
            ->leftJoin('images', function ($join){
                $join->on('annonces.id', '=', 'images.annonce_id');
            })
            ->join('categories', 'annonces.categorie_id', '=', 'categories.id')
            ->select('annonces.*','images.url','categories.name')
            ->groupBy('annonces.id')
            ->orderBy('annonces.created_at', 'DESC')
            ->paginate(10);
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
        foreach ($request->url as $u) {
            $pic = new Image();
            $pic->url = $u;
            $pic->annonce_id = $annonce->id;
            $pic->className = 'annonce';
            $pic->save();
        }
        return response()->json(['message'=>'annonce created successfully']);
    }

    public function show($id)
    {
        $annonce = Annonce::findOrFail($id);
        $pictures = $annonce->images()->get();
        $user = $annonce->user()->get();
        $categorie = $annonce->categorie()->get();
        $currentUser = auth()->user();
        $comments = \DB::table('commentaires')
            ->where('commentaires.annonce_id', $annonce->id)
            ->leftjoin('users', 'commentaires.user_id', '=', 'users.id')
            ->leftjoin('images', 'users.id', '=', 'images.user_id')
            ->select('images.url','users.*','commentaires.*')
            ->orderBy('commentaires.created_at', 'ASC')
            ->get();
        return response()->json(['annonce' => $annonce , 'images' => $pictures ,
            'user' => $user , 'categorie' => $categorie , 'currentUser' => $currentUser, 'commentaires' => $comments],200);
    }

    public function update(Request $request)
    {
        $this->validator($request->all());
        $categorie = Categorie::where(['name'=>$request->name])->firstOrFail();
        $user = auth()->user();
        $annonce = Annonce::find($request->id);
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
            foreach ($request->url as $u) {
                $pic = new Image();
                $pic->url = $u;
                $pic->annonce_id = $annonce->id;
                $pic->className = 'annonce';
                $pic->save();
            }
            return response()->json(['message'=>'annonce updated successfully'],200);
    }

    public function cloturer($id){
        $user = auth()->user();
        $annonce = Annonce::where('id',$id)->get();
        if($annonce[0]->user_id==$user->id){
            $annonce[0]->update([
                'status' => 0
            ]);
            return response()->json(['message'=>'annonce updated successfully'],200);
        }else{
            return response()->json(['message'=>'Unprocessable entity'],419);
        }
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
