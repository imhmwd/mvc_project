<?php

use System\Database\Traits\HasCRUD;

function dd($var){
    echo "<pre>";
    print_r($var);
    exit;
}

require_once ("../config/app.php");
require_once ("../config/database.php");


require_once ("../routes/web.php");
require_once ("../routes/api.php");



// $categories = \App\Category::all();
// foreach($categories as $category){
//     echo $category->id . '--' . $category->title . '--';
// }

// $categories = \App\Category::paginate(2);
// foreach($categories as $category){
//     echo $category->id . '--' . $category->name . '--';
// }


// $post = \App\Post::find(1);
// dd($post);

// $categories = \App\Category::where('id', '>', 1)->get();
// foreach($categories as $category){
//     echo $category->id . '--' . $category->name . '--';
// }


// $posts = \App\Category::find(1)->posts()->get();
// foreach($posts as $post){
//     echo $post->id . '--' . $post->title . '--';
// }


// $post = \App\Post::find(1);
// $category = $post->category();
// dd($category);

// $users = \App\Role::find(1)->users()->get();
// foreach($users as $user){
//     echo $user->id . '--' . $user->username . '--';
// }


// $roles = \App\User::find(1)->roles()->get();
// foreach($roles as $role){
//     echo $role->id . '--' . $role->name . '--';
// }




$routing = new \System\Router\Routing();
$routing->run();