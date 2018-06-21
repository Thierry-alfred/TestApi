<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\article;
use Validator;

class ArticleController extends ApiController
{
    public  function index(){
        return article::all();
    }


    public function show($id){
        return article::all()->where('id', $id);
    }


    public function GetArticleByIDUser($iduser){
        return article::all()->where('user_id', $iduser);
    }
     public function store(Request $request){
         $rules = array (
             'titre' => 'required',
             'description' => 'required',
             'user_id' => 'required'
         );

        try{
            $validator = Validator::make($request->all(), $rules);
            if ($validator-> fails()){
                return $this->respondValidationError('VERIFIER VOS CHAMPS', $validator->errors());
            }

            else{

                $article = article::create([
                    'titre' => $request['titre'],
                    'description' => $request['description'],
                    'user_id' => $request['user_id'],
                    'fichier_id' => null
                ]);
                return $this->respond([
                    'status' => 'success',
                    'status_code' => $this->getStatusCode(),
                    'message' => 'ARTICLE CREE',
                    'data' => $article
                ]);
            }
        }catch(\Exception $e){
            return $this->respondInternalError($e->getMessage());
        }
     }
}
