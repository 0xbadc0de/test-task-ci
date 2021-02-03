<?php

namespace Model;

use App;
use CI_Emerald_Model;
use Exception;

class Transaction_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'transaction';

    /** @var int */
    public $user_id;
    /** @var string */
    public $transaction_type;
    /** @var string */
    public $transaction_subject;
    /** @var int */
    public $transaction_record;
    /** @var string */
    public $wallet_type;
    /** @var string */
    public $description;
    /** @var float */
    public $amount;

    /** @var string */
    public $time_created;
    /** @var string */
    protected $time_updated;

    // generated
    public $user;

    /**
     * @return int
     */
    public function get_user_id(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function set_user_id(int $user_id)
    {
        $this->user_id = $user_id;
        return $this->save('user_id', $user_id);
    }

    /**
     * @return float
     */
    public function get_amount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function set_amount(float $amount)
    {
        $this->amount = $amount;
        return $this->save('amount', $amount);
    }

    /**
     * @return string
     */
    public function get_wallet_type(): string
    {
        return $this->wallet_type;
    }

    /**
     * @param string $wallet_type
     * @return bool
     */
    public function set_wallet_type(string $wallet_type): bool
    {
        $this->wallet_type = $wallet_type;
        return $this->save('wallet_type', $wallet_type);
    }

    /**
     * @return string
     */
    public function get_description(): string
    {
        return $this->description;
    }

    /**
     * @param float $description
     * @return bool
     */
    public function set_description(float $description): bool
    {
        $this->description = $description;
        return $this->save('description', $description);
    }

    /**
     * @return string
     */
    public function get_transaction_subject(): string
    {
        return $this->transaction_subject;
    }

    /**
     * @param string $transaction_subject
     * @return bool
     */
    public function set_transaction_subject(string $transaction_subject)
    {
        $this->transaction_subject = $transaction_subject;
        return $this->save('transaction_subject', $transaction_subject);
    }

    /**
     * @return string
     */
    public function get_transaction_type(): string
    {
        return $this->transaction_type;
    }

    /**
     * @param string $transaction_type
     * @return bool
     */
    public function set_transaction_type(string $transaction_type)
    {
        $this->transaction_type = $transaction_type;
        return $this->save('transaction_type', $transaction_type);
    }

    /**
     * @return string
     */
    public function get_transaction_record(): string
    {
        return $this->transaction_record;
    }

    /**
     * @param int $transaction_record
     * @return bool
     */
    public function set_transaction_record(int $transaction_record)
    {
        $this->transaction_record = $transaction_record;
        return $this->save('transaction_record', $transaction_record);
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
    public function set_time_updated(int $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }

    // generated

    /**
     * @return User_model
     */
    public function get_user():User_model
    {
        if (empty($this->user))
        {
            try {
                $this->user = new User_model($this->get_user_id());
            } catch (Exception $exception)
            {
                $this->user = new User_model();
            }
        }
        return $this->user;
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

    public function delete()
    {
        $this->is_loaded(TRUE);
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }

}
