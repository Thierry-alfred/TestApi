<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\article;

class ArticleController extends ApiController
{
    public  function index(){
        return article::all();
    }
     public function store(Request $request){
         $rules = array (
             'titre' => 'required',
             'description' => 'required',
             'user_id' => 'required'
         );

         $validator = Validator::make($request->all(), $rules);
         if ($validator-> fails()){
             return $this->respondValidationError('VERIFIER VOS CHAMPS', $validator->errors());
         }

         else{

             $article = article::create([
                 'titre' => $request['titre'],
                 'description' => $request['description'],
                 'user_id' => $request['user_id'],
                 'fichier_id' => $request['fichier_id']
             ]);
             return $this->respond([
                 'status' => 'success',
                 'status_code' => $this->getStatusCode(),
                 'message' => 'ARTICLE CREE',
                 'data' => $article
             ]);
         }
     }
}
