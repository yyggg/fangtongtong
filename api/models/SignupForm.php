<?php
namespace api\models;

use api\models\User;
use common\models\InviteReg;
use common\models\Task;
use common\models\TaskUser;
use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $phone;
    public $password;
    public $sms_code;
    public $invite_code;
    public $source;
    public $source_id;

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['phone', 'trim'],
            [['sms_code', 'password', 'phone'], 'required'],
            [['phone'],'match','pattern'=>'/^[1][3578][0-9]{9}$/'],
            ['phone', 'unique', 'targetClass' => '\api\models\User', 'message' => '手机号已被注册'],
            ['password', 'string', 'max' => 18, 'min' => 6],
            //['agree', 'required', 'requiredValue'=>true,'message'=>'请确认是否同意隐私权协议条款'],
        ];
    }


    public function signup()
    {
        if (!$this->validate())
        {
            return false;
        }

        $user = new User();
        $user->phone = $this->phone;
        $user->password = $user->setPassword($this->password);

        if ($this->invite_code)
        {
            $userId = $user::inviteDecode($this->invite_code);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 插入记录
                $user->save();

                InviteReg::createLevelRelation($userId, $user->getId(), $this->source, $this->source_id);

                // 更新任务状态
                if ($this->source == 1)
                {
                    if ($this->source_id)
                    {
                        $count = InviteReg::find()
                            ->where(['source' => $this->source, 'source_id' => $this->source_id])
                            ->count('task_user_id');
                        $task = Task::findOne(['task_id' => $this->source_id]);

                        if ($count >= $task->condition)
                        {
                            $model = TaskUser::findOne(['task_id' => $this->source_id, 'status' => 1, 'user_id' => $userId]);
                            $model->status = 3;
                            $model->save(false);
                        }
                    }

                }
                $transaction->commit();

            } catch (\Exception $e) {

                $transaction->rollBack();
                return response([], '20001');
            }
        }
        else
        {
            if (!$user->save())
            {
                return false;
            }

            return true;
        }
    }


    public function attributeLabels()
    {
        return [
            'phone'    => '手机号',
            'sms_code' => '手机验证码',
            'password' => '密码',
        ];
    }
}
