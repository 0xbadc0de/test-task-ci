<?php

namespace Model;

use App;
use CI_Emerald_Model;
use Exception;
use stdClass;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 27.01.2020
 * Time: 10:10
 */
class User_model extends CI_Emerald_Model {
    const CLASS_TABLE = 'user';


    /** @var string */
    protected $email;
    /** @var string */
    protected $password;
    /** @var string */
    protected $personaname;
    /** @var string */
    protected $profileurl;
    /** @var string */
    protected $avatarfull;
    /** @var int */
    protected $rights;
    /** @var int */
    protected $likes_balance;
    /** @var float */
    public $wallet_balance;
    /** @var float */
    protected $wallet_total_refilled;
    /** @var float */
    protected $wallet_total_withdrawn;
    /** @var string */
    protected $time_created;
    /** @var string */
    protected $time_updated;


    private static $_current_user;

    protected $hidden = ['password'];

    /**
     * @return string
     */
    public function get_email(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function set_email(string $email)
    {
        $this->email = $email;
        return $this->save('email', $email);
    }

    /**
     * @return string|null
     */
    public function get_password(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function set_password(string $password)
    {
        $this->password = $password;
        return $this->save('password', $password);
    }

    /**
     * @return string
     */
    public function get_personaname(): string
    {
        return $this->personaname;
    }

    /**
     * @param string $personaname
     *
     * @return bool
     */
    public function set_personaname(string $personaname)
    {
        $this->personaname = $personaname;
        return $this->save('personaname', $personaname);
    }

    /**
     * @return string
     */
    public function get_avatarfull(): string
    {
        return $this->avatarfull;
    }

    /**
     * @param string $avatarfull
     *
     * @return bool
     */
    public function set_avatarfull(string $avatarfull)
    {
        $this->avatarfull = $avatarfull;
        return $this->save('avatarfull', $avatarfull);
    }

    /**
     * @return int
     */
    public function get_rights(): int
    {
        return $this->rights;
    }

    /**
     * @param int $rights
     *
     * @return bool
     */
    public function set_rights(int $rights)
    {
        $this->rights = $rights;
        return $this->save('rights', $rights);
    }

    /**
     * Check wallet balance
     *
     * @param float $sum
     * @return bool
     */
    public function check_balance(float $sum): bool
    {
        return $this->wallet_balance >= $sum;
    }

    /**
     * @return float
     */
    public function get_likes_balance(): float
    {
        return $this->likes_balance;
    }

    /**
     * @param float $likes_balance
     * @return bool
     */
    public function set_likes_balance(float $likes_balance): bool
    {
        $this->likes_balance = $likes_balance;
        return $this->save('likes_balance', $likes_balance);
    }

    /**
     * @param float $sum
     * @return bool
     */
    public function check_likes_balance(float $sum): bool
    {
        return $this->likes_balance >= $sum;
    }

    /**
     * @param float $sum
     * @param string $description
     * @param string $transactionSubject
     * @param int|null $recordId
     * @return bool
     */
    public function increase_likes_balance(float $sum, string $description, string $transactionSubject, int $recordId = null): bool
    {
        if ($sum < 0)
            return false;

        $this->likes_balance += $sum;

        Transaction_model::create([
            'user_id' => User_model::get_user()->id,
            'transaction_type' => Transaction_type::TRANSACTION_TYPE_REFILL,
            'transaction_subject' => $transactionSubject,
            'transaction_record' => $recordId,
            'wallet_type' => Wallet_type::WALLET_TYPE_LIKES,
            'description' => $description,
            'amount' => $sum
        ]);

        return $this->save('likes_balance', $this->likes_balance);
    }

    /**
     * @param float $sum
     * @param string $description
     * @param string $transactionSubject
     * @param int|null $recordId
     * @return bool
     */
    public function decrease_likes_balance(float $sum, string $description, string $transactionSubject, int $recordId = null): bool
    {
        if ($sum < 0)
            return false;

        $this->likes_balance -= $sum;
        if ($this->likes_balance < 0)
            $this->likes_balance = 0;

        Transaction_model::create([
            'user_id' => User_model::get_user()->id,
            'transaction_type' => Transaction_type::TRANSACTION_TYPE_REFILL,
            'transaction_subject' => $transactionSubject,
            'transaction_record' => $recordId,
            'wallet_type' => Wallet_type::WALLET_TYPE_LIKES,
            'description' => $description,
            'amount' => $sum
        ]);

        return $this->save('likes_balance', $this->likes_balance);
    }

    /**
     * @return float
     */
    public function get_wallet_balance(): float
    {
        return $this->wallet_balance;
    }

    /**
     * @param float $wallet_balance
     *
     * @return bool
     */
    public function set_wallet_balance(float $wallet_balance)
    {
        $this->wallet_balance = $wallet_balance;
        return $this->save('wallet_balance', $wallet_balance);
    }

    /**
     * Increase wallet balance
     *
     * @param float $sum
     * @param string $description
     * @param string $transactionSubject
     * @param int|null $recordId
     * @return bool
     */
    public function increase_wallet_balance(float $sum, string $description, string $transactionSubject, int $recordId = null): bool
    {
        if ($sum < 0)
            return false;

        $this->wallet_balance += $sum;

        $this->set_wallet_total_refilled($this->get_wallet_total_refilled() + $sum);

        Transaction_model::create([
            'user_id' => User_model::get_user()->id,
            'transaction_type' => Transaction_type::TRANSACTION_TYPE_REFILL,
            'transaction_subject' => $transactionSubject,
            'transaction_record' => $recordId,
            'wallet_type' => Wallet_type::WALLET_TYPE_GENERIC,
            'description' => $description,
            'amount' => $sum
        ]);

        return $this->save('wallet_balance', $this->wallet_balance);
    }

    /**
     * Decrease wallet balance
     *
     * @param float $sum
     * @param string $description
     * @param string $transactionSubject
     * @param int|null $recordId
     * @return bool
     */
    public function decrease_wallet_balance(float $sum, string $description, string $transactionSubject, int $recordId = null): bool
    {
        if ($sum < 0)
            return false;

        $this->wallet_balance -= $sum;
        if ($this->wallet_balance < 0)
            $this->wallet_balance = 0;

        $this->set_wallet_total_withdrawn($this->get_wallet_total_withdrawn() + $sum);

        Transaction_model::create([
            'user_id' => User_model::get_user()->id,
            'transaction_type' => Transaction_type::TRANSACTION_TYPE_WITHDRAW,
            'transaction_subject' => $transactionSubject,
            'transaction_record' => $recordId,
            'wallet_type' => Wallet_type::WALLET_TYPE_GENERIC,
            'description' => $description,
            'amount' => $sum
        ]);

        return $this->save('wallet_balance', $this->wallet_balance);
    }

    /**
     * @return float
     */
    public function get_wallet_total_refilled(): float
    {
        return $this->wallet_total_refilled;
    }

    /**
     * @param float $wallet_total_refilled
     *
     * @return bool
     */
    public function set_wallet_total_refilled(float $wallet_total_refilled)
    {
        $this->wallet_total_refilled = $wallet_total_refilled;
        return $this->save('wallet_total_refilled', $wallet_total_refilled);
    }

    /**
     * @return float
     */
    public function get_wallet_total_withdrawn(): float
    {
        return $this->wallet_total_withdrawn;
    }

    /**
     * @param float $wallet_total_withdrawn
     *
     * @return bool
     */
    public function set_wallet_total_withdrawn(float $wallet_total_withdrawn)
    {
        $this->wallet_total_withdrawn = $wallet_total_withdrawn;
        return $this->save('wallet_total_withdrawn', $wallet_total_withdrawn);
    }

    /**
     * @return string
     */
    public function get_time_created(): string
    {
        return $this->time_created;
    }

    /**
     * @param string $time_created
     *
     * @return bool
     */
    public function set_time_created(string $time_created)
    {
        $this->time_created = $time_created;
        return $this->save('time_created', $time_created);
    }

    /**
     * @return string
     */
    public function get_time_updated(): string
    {
        return $this->time_updated;
    }

    /**
     * @param string $time_updated
     *
     * @return bool
     */
    public function set_time_updated(string $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }


    function __construct($id = NULL)
    {
        parent::__construct();
        $this->set_id($id);
    }

    public function reload(bool $for_update = FALSE)
    {
        parent::reload($for_update);

        return $this;
    }

    public static function create(array $data)
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();
        return new static(App::get_ci()->s->get_insert_id());
    }

    public function getByEmail(string $email)
    {
        return (new self())->set(App::get_ci()->s->from(self::CLASS_TABLE)->where(['email' => $email])->one());
    }

    public function delete()
    {
        $this->is_loaded(TRUE);
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }

    /**
     * @return self[]
     * @throws Exception
     */
    public static function get_all():array
    {

        $data = App::get_ci()->s->from(self::CLASS_TABLE)->many();
        $ret = [];
        foreach ($data as $i)
        {
            $ret[] = (new self())->set($i);
        }
        return $ret;
    }


    /**
     * Getting id from session
     * @return integer|null
     */
    public static function get_session_id(): ?int
    {
        return App::get_ci()->session->userdata('id');
    }

    /**
     * @return bool
     */
    public static function is_logged():bool
    {
        $steam_id = intval(self::get_session_id());
        return $steam_id > 0;
    }



    /**
     * Returns current user or empty model
     * @return User_model
     */
    public static function get_user()
    {
        if (! is_null(self::$_current_user)) {
            return self::$_current_user;
        }
        if ( ! is_null(self::get_session_id()))
        {
            self::$_current_user = new self(self::get_session_id());
            return self::$_current_user;
        } else
        {
            return new self();
        }
    }



    /**
     * @param User_model|User_model[] $data
     * @param string $preparation
     * @return stdClass|stdClass[]
     * @throws Exception
     */
    public static function preparation($data, $preparation = 'default')
    {
        switch ($preparation)
        {
            case 'main_page':
                return self::_preparation_main_page($data);
            case 'default':
                return self::_preparation_default($data);
            default:
                throw new Exception('undefined preparation type');
        }
    }

    /**
     * @param User_model $data
     * @return stdClass
     */
    private static function _preparation_main_page($data)
    {
        $o = new stdClass();

        $o->id = $data->get_id();

        $o->personaname = $data->get_personaname();
        $o->avatarfull = $data->get_avatarfull();

        $o->time_created = $data->get_time_created();
        $o->time_updated = $data->get_time_updated();


        return $o;
    }


    /**
     * @param User_model $data
     * @return stdClass
     */
    private static function _preparation_default($data)
    {
        $o = new stdClass();

        if (!$data->is_loaded())
        {
            $o->id = NULL;
        } else {
            $o->id = $data->get_id();

            $o->personaname = $data->get_personaname();
            $o->avatarfull = $data->get_avatarfull();

            $o->wallet_balance = $data->get_wallet_balance();

            $o->time_created = $data->get_time_created();
            $o->time_updated = $data->get_time_updated();
        }

        return $o;
    }

    /**
     * Is user admin?
     *
     * @return bool
     */
    public function is_admin()
    {
        return (int)$this->rights === User_role::ROLE_ADMIN;
    }

}
