<?php

use Slim\Http\Request;
use Slim\Http\Response;
use simplon\entities\User;
use simplon\dao\DaoUser;
use simplon\entities\Post;
use simplon\dao\DaoPost;

// Routes


$app->get('/', function (Request $request, Response $response, array $args) {
    //On instancie le dao
    $dao = new DaoUser();
    //On récupère les Persons via la méthode getAll
    $users = $dao->getAll();
    //On passe les persons à la vue index.twig
    return $this->view->render($response, 'index.twig', [
        'users' => $users, 'currUser' => $_SESSION['user']
    ]);
})->setName('index');



$app->get('/adduser', function (Request $request, Response $response, array $args) {
    return $this->view->render($response, 'adduser.twig');
})->setName('adduser');

$app->post('/adduser', function (Request $request, Response $response, array $args) {
    //On récupère les données du formulaire
    $form = $request->getParsedBody();
    //On crée une Person à partir de ces données
    $newUser = new User($form['name'], $form['email'], $form['password']);
    //On instancie le DAO
    $dao = new DaoUser();
    //On utilise la méthode add du DAO en lui donnant la Person qu'on vient de créer
    $dao->add($newUser);
    //On affiche la même vue que la route en get
    $redirectUrl = $this->router->pathFor('index');
    return $response->withRedirect($redirectUrl);
})->setName('adduser');



$app->get('/updateuser/{id}', function (Request $request, Response $response, array $args) {
    //On instancie le DAO
    $dao = new DaoUser;
    //On récupère la Person à partir de l'id
    $user = $dao->getById($args['id']);
    // On affiche la vue du formulaire d'update d'une peronne
    return $this->view->render($response, 'updateuser.twig', [
        'user' => $user
    ]); 
})->setName('updateuser');

$app->post('/updateuser/{id}', function (Request $request, Response $response, array $args) {
    //On instancie le DAO
    $dao = new DaoUser;
    //On récupère les données du formulaire
    $postData = $request->getParsedBody();
    //On récupère la Person à partir de l'id
    $user = $dao->getById($args['id']);
    //On met à jour son nom, sa date de naissance et son genre
    $user->setName($postData['name']);
    $user->setEmail($postData['email']);
    $user->setPassword($postData['password']);
    //On update la personne
    $dao->update($user);
    //On récupère l'URL da la route index (page d'accueil)
    $redirectUrl = $this->router->pathFor('index');
    //On redirige l'utilisateur sur la page d'accueil
    return $response->withRedirect($redirectUrl);
})->setName('updateuser');

$app->get('/deleteuser/{id}', function (Request $request, Response $response, array $args) {
    //On instancie le DAO
    $dao = new DaoUser;
    //On delete la personne
    $dao->delete($args['id']);
    //On récupère l'URL da la route index (page d'accueil)
    $redirectUrl = $this->router->pathFor('index');
    //On redirige l'utilisateur sur la page d'accueil
    return $response->withRedirect($redirectUrl);
})->setName('deleteuser');



    // LOGIN

$app->get('/login', function (Request $request, Response $response, array $args) {
    return $this->view->render($response, 'login.twig');   
})->setName('login');

$app->post('/login', function (Request $request, Response $response, array $args) {
    $dao = new DaoUser();
    $form = $request->getParsedBody();
    $user = $dao->getEmail($form['email']);

    $form['isLogged'] = (!empty($user) && $form['email'] === $user->getEmail() && $form['password'] === $user->getPassword());
    $_SESSION['user']= $user;
    if ($form['isLogged']) {
        $redirectUrl = $this->router->pathFor('index',[
            'id' => $user->getId()
            ]);
        return $response->withRedirect($redirectUrl);
    } else {
        return $this->view->render($response, 'index.twig', [
            'user' => $user
        ]);
    }
})->setName('login');


        // LOG OUT

$app->get('/logout', function (Request $request, Response $response, array $args) {
    session_destroy();
    $redirectUrl = $this->router->pathFor('index');
    return $response->withRedirect($redirectUrl);
})->setName('logout');


        // ADD POST

$app->get('/addpost', function (Request $request, Response $response, array $args) {
    return $this->view->render($response, 'addpost.twig');   
})->setName('addpost');
    
$app->post('/addpost', function (Request $request, Response $response, array $args) {
        $form = $request->getParsedBody();
        $newPost = new Post($form['title'], $form['content']);
        $newDaoPost = new DaoPost();
        $newDaoPost->add($newPost, $_SESSION['user']->getId());
        $redirectUrl = $this->router->pathFor('index');
        return $response->withRedirect($redirectUrl);
})->setName('addpost');



