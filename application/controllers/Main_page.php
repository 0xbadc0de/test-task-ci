<?php

use Model\Boosterpack_model;
use Model\Login_model;
use Model\Post_model;
use Model\Transaction_info;
use Model\User_model;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    const LIKE_COST = 1;

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
        // 4th task  add money to user logic

        // This isn't working (at least on nginx, php-fpm), have no time to investigate
        // TODO: Investigate
        $sum = App::get_ci()->input->post('sum');

        // so do the bad way
        $input = json_decode(App::get_ci()->input->raw_input_stream, true);

        $sum = floatval(@$input['sum']);

        if (empty($sum) || $sum < 0.0){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        if (!User_model::get_user()->is_admin()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_ACCESS);
        }

        User_model::get_user()->increase_wallet_balance(
            $sum,
            'Incoming transaction (topup)',
            Transaction_info::TRANSACTION_TYPE_TOPUP
        );

        return $this->response_success(['amount' => User_model::get_user()->wallet_balance]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

    public function buy_boosterpack(){
        // 5th task add money to user logic

        // This isn't working (at least on nginx, php-fpm), have no time to investigate
        // TODO: Investigate
        $id = App::get_ci()->input->post('id');

        // so do the bad way
        $input = json_decode(App::get_ci()->input->raw_input_stream, true);

        $id = intval(@$input['id']);

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        if (empty($id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $booster_pack = new Boosterpack_model($id);
        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        if (!User_model::get_user()->check_balance($booster_pack->get_price())) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_INSUFFICIENT_BALANCE);
        }

        $topRandom = $booster_pack->get_price() + $booster_pack->get_bank();
        $result = rand(1, $topRandom);
        $bank = $booster_pack->get_price() - $result;
        if ($bank < 0) {
            $bank = 1;
        }
        $booster_pack->set_bank($bank);

        User_model::get_user()->decrease_wallet_balance(
            $booster_pack->get_price(),
            sprintf('Purchase of booster pack #%d', $booster_pack->get_id()),
            Transaction_info::TRANSACTION_TYPE_BOOSTER_PACK,
            $booster_pack->get_id()
        );

        // Forgot about that in previous commit
        User_model::get_user()->increase_wallet_balance(
            $result,
            sprintf('Opened booster pack #%d', $booster_pack->get_id()),
            Transaction_info::TRANSACTION_TYPE_BOOSTER_PACK,
            $booster_pack->get_id()
        );

        return $this->response_success(['amount' => $result]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }


    public function like($type, $post_id){
        // 3rd task add like post\comment logic

        $post_id = intval($post_id);

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        if (empty($post_id) || empty($type)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        if (!User_model::get_user()->check_balance(self::LIKE_COST)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_INSUFFICIENT_BALANCE);
        }

        $likes = null;
        if ($type == 'post') {
            try {
                $post = new Post_model($post_id);
                $post->increment_likes_count();
                $likes = $post->get_likes_count();

                // Decrease wallet balance on success
                // We can use DB Transactions, but now i don't have a time
                User_model::get_user()->decrease_wallet_balance(
                    self::LIKE_COST,
                    sprintf('Like of post #%d', $post->get_id()),
                    Transaction_info::TRANSACTION_TYPE_LIKE,
                    $post->get_id()
                );
            } catch (EmeraldModelNoDataException $ex) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
            }
        }

        // TODO: Done
        if ($type == 'comment') {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_UNAVAILABLE);
        }

        return $this->response_success(['likes' => $likes]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

}
