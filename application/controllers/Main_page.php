<?php

use Model\Login_model;
use Model\Post_model;
use Model\User_model;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id){ // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment(){ // or can be App::get_ci()->input->post('news_id') , but better for GET REQUEST USE THIS ( tests )

        $post_id = App::get_ci()->input->post('post_id');

        // This isn't working (at least on nginx, php-fpm), have no time to investigate
        // TODO: Investigate
        $post_id = App::get_ci()->input->post('post_id');
        $message = App::get_ci()->input->post('message');

        // so do the bad way
        $input = json_decode(App::get_ci()->input->raw_input_stream, true);

        $post_id = @$input['post_id'];
        $message = @$input['message'];

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $post_id = intval($post_id);

        if (empty($post_id) || empty($message)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        // 2 nd task Comment
        // TODO: Reply_to realization
        $post->comment($message);

        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function login()
    {
        // Right now for tests we use from contriller

        // This isn't working, have no time to investigate
        // TODO: Investigate
        $login = App::get_ci()->input->post('login');
        $password = App::get_ci()->input->post('password');

        // so do the bad way
        $input = json_decode(App::get_ci()->input->raw_input_stream, true);

        $login = @$input['login'];
        $password = @$input['password'];

        if (empty($login) || empty($password)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        // But data from modal window sent by POST request.  App::get_ci()->input...  to get it. - nope, you can't :D

        $user = User_model::getByEmail($login);
        if (!$user || !password_verify($password, $user->get_password())) {
            return $this->response_error('invalid_credentials');
        }

        // password_hash('123321', PASSWORD_BCRYPT);

        Login_model::start_session($user);

        return $this->response_success(['user' => $user->toArray()]);
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money(){
        // todo: 4th task  add money to user logic
        return $this->response_success(['amount' => rand(1,55)]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

    public function buy_boosterpack(){
        // todo: 5th task add money to user logic
        return $this->response_success(['amount' => rand(1,55)]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }


    public function like(){
        // todo: 3rd task add like post\comment logic
        return $this->response_success(['likes' => rand(1,55)]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

}
