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
class Comment_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'comment';


    /** @var int */
    protected $user_id;
    /** @var int */
    protected $assing_id;
    /** @var string */
    public $text;
    /** @var int */
    public $replies_count;

    /** @var string */
    public $time_created;
    /** @var string */
    protected $time_updated;

    // generated
    protected $comments;
    protected $likes;
    protected $user;
    /** @var array */
    public $replies;


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
     * @return int
     */
    public function get_replies_count(): int
    {
        return $this->replies_count;
    }

    /**
     * @param int $assing_id
     *
     * @return bool
     */
    public function set_replies_count(int $repliesCount)
    {
        $this->replies_count = $repliesCount;
        return $this->save('replies_count', $repliesCount);
    }

    /**
     * @return int
     */
    public function get_assing_id(): int
    {
        return $this->assing_id;
    }

    /**
     * @param int $assing_id
     *
     * @return bool
     */
    public function set_assing_id(int $assing_id)
    {
        $this->assing_id = $assing_id;
        return $this->save('assing_id', $assing_id);
    }


    /**
     * @return string
     */
    public function get_text(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    public function set_text(string $text)
    {
        $this->text = $text;
        return $this->save('text', $text);
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
     * @return mixed
     */
    public function get_likes()
    {
        return $this->likes;
    }

    /**
     * @return mixed
     */
    public function get_comments()
    {
        return $this->comments;
    }

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

    /**
     * @param int $assting_id
     * @return self[]
     * @throws Exception
     */
    public static function get_all_by_assign_id(int $assting_id)
    {
        $data = App::get_ci()->s->from(self::CLASS_TABLE)
            ->where(['assign_id' => $assting_id])
            ->where(['reply_to' => null])
            ->orderBy('time_created','ASC')
            ->many();

        $ret = [];
        foreach ($data as $i)
        {
            $comment = (new self())->set($i);
            if ((int)$i['replies_count'] > 0) {
                $replies = self::get_replies_tree($i['id']);
                $comment->{'replies'} = self::build_tree($replies, $comment->id);
            }
            $ret[] = $comment;
        }
        return $ret;
    }

    function build_tree(array &$comments, $replyTo = 0) {
        $final = [];

        foreach ($comments as $comment) {
            if ($comment->reply_to == $replyTo) {
                $replies = self::build_tree($comments, $comment->id);
                if ($replies) {
                    $comment->replies = $replies;
                }
                $final[] = $comment;
            }
        }
        return $final;
    }

    /**
     * @param int $postId
     * @return self[]
     * @throws Exception
     */
    public static function get_replies_tree(int $postId)
    {
        $data = App::get_ci()->s->sql("WITH RECURSIVE nested_comments (id, reply_to, text, replies_count, personaname, lvl) AS
        (
          SELECT c.id, c.reply_to, c.text, c.replies_count, u.personaname, 0 lvl
            FROM comment AS c
            JOIN user AS u ON u.id = c.user_id
            WHERE reply_to = ${postId}
          UNION ALL
          SELECT c.id, c.reply_to, c.text, c.replies_count, u.personaname, nc.lvl + 1
            FROM nested_comments AS nc JOIN comment AS c
              ON nc.id = c.reply_to
            JOIN user AS u ON u.id = c.user_id
        )SELECT * FROM nested_comments ORDER BY lvl")->many();

        $ret = [];
        foreach ($data as $i)
        {
            $ret[] = (new self())->set($i);
        }
        return $ret;
    }

    /**
     * @param self|self[] $data
     * @param string $preparation
     * @return stdClass|stdClass[]
     * @throws Exception
     */
    public static function preparation($data, $preparation = 'default')
    {
        switch ($preparation)
        {
            case 'full_info':
                return self::_preparation_full_info($data);
            default:
                throw new Exception('undefined preparation type');
        }
    }


    /**
     * @param self[] $data
     * @return stdClass[]
     */
    private static function _preparation_full_info($data)
    {
        $ret = [];

        foreach ($data as $d){
            $o = new stdClass();

            $o->id = $d->get_id();
            $o->text = $d->get_text();
            $o->replies = @$d->replies;
            $o->replies_count = @$d->replies_count;

            $o->user = User_model::preparation($d->get_user(),'main_page');

            $o->likes = rand(0, 25);

            $o->time_created = $d->get_time_created();
            $o->time_updated = $d->get_time_updated();

            $ret[] = $o;
        }


        return $ret;
    }


}
