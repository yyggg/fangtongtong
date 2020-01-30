<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-02 15:30
 */

namespace api\modules\v1\controllers;

use common\models\Comment;
use common\models\GroupBuying;
use common\models\GroupBuyingTpl;
use common\models\HouseType;
use common\models\PropertiesAdviserRelation;
use common\models\PropertiesAnswers;
use common\models\PropertiesAsk;
use common\models\PropertiesInformation;
use common\models\Task;
use common\models\User;
use Yii;
use api\controllers\BaseController;
use common\models\Properties;
use common\models\PropertiesLabel;
use common\models\PropertiesLabelRelation;
use common\models\Region;

class PropertiesController extends BaseController
{
    /**
     * 首页接口
     * @return array
     */
    public function actionIndex()
    {
        $data = [];
        $cityCode = Yii::$app->request->get('city_code', '');

        $sql = "SELECT
                a.name, a.down_payment_id, a.properties_id
            FROM
                ftt_properties AS a 
            WHERE
                ( SELECT COUNT( * ) FROM ftt_properties AS b WHERE b.down_payment_id = a.down_payment_id AND b.properties_id >= a.properties_id ) <= 3 
            ORDER BY
                a.down_payment_id,
                a.properties_id DESC";

        $model = Properties::findBySql($sql)->asArray()->all();

        $tmpArr = [];
        foreach ($model as $v){
            $tmpArr[$v['down_payment_id']]['typename'] = Yii::$app->params['down_payment_name'][$v['down_payment_id']];
            $tmpArr[$v['down_payment_id']]['list'][] = $v;
        }
        $data['down_payment'] = $tmpArr;
        // 推荐楼盘
        $model = Properties::find()
            ->alias('a')
            ->select([
                'a.properties_id','a.name','a.pic','a.price_metre','a.sale_status',
                'MIN(e.square_metre) AS square_metre_min', 'MAX(e.square_metre) AS square_metre_max',
                'GROUP_CONCAT(DISTINCT c.label_name) AS label_name','MAX(d.region_name) AS region_name'
            ])
            ->leftJoin(PropertiesLabelRelation::tableName() . ' b', 'a.properties_id=b.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' c', 'c.properties_label_id=b.properties_label_id')
            ->leftJoin(Region::tableName() . ' d', 'd.region_code=a.region_code')
            ->leftJoin(HouseType::tableName() . ' e', 'e.properties_id=a.properties_id')
            ->where(['a.city_code' => $cityCode, 'a.recommend' => '1'])
            ->groupBy('a.properties_id')
            ->orderBy('a.properties_id desc')
            ->limit(2)
            ->asArray()
            ->all();

        foreach ($model as $k => $v) {
            $v['sale_status'] = Yii::$app->params['sale_status'][$v['sale_status']];
            if ($v['pic'])
            {
                $v['pic'] = json_decode($v['pic']);
            }
            $model[$k] = $v;
        }
        $data['recommend'] = $model;

        return response($data);
    }


    /**
     * 首页接口
     * @return array
     */
    public function actionMap()
    {
        $unitPriceMin   = ''; // 单价最小值
        $unitPriceMax   = ''; // 单价最大值
        $totalPriceMin  = ''; // 总价最小值
        $totalPriceMax  = ''; // 总价最大值
        $squareMetreMin = ''; // 面积最小值
        $squareMetreMax = ''; // 面积最大值
        $openTimeMin    = ''; // 开盘时间最小值
        $openTimeMax    = ''; // 开盘时间最大值

        $sort = 'a.properties_id DESC'; // 默认排序

        $cityCode          = Yii::$app->request->post('city_code', 110100); // 定位的城市码
        $sortKey       = Yii::$app->request->post('sort_key', ''); // 排序键
        $sortVal       = Yii::$app->request->post('sort_val', ''); // 排序值
        $regionCode    = Yii::$app->request->post('region_code', ''); // 区码 城市筛选
        $unitPriceKey  = Yii::$app->request->post('unit_price', ''); // 单价筛选
        $totalPriceKey = Yii::$app->request->post('total_price', ''); // 总价筛选
        $houseType     = Yii::$app->request->post('house_type', ''); // 户型筛选


        $highRemuneration = Yii::$app->request->post('high_remuneration', ''); // 高佣金筛选
        $downPayment      = Yii::$app->request->post('down_payment', ''); // 首付筛选
        $fast_get_remuneration      = Yii::$app->request->post('fast_get_remuneration', ''); // 快速拿佣筛选

        $propertyType  = Yii::$app->request->post('property_type_id', ''); // 物业类型筛选
        $propertyType  = explode(',', $propertyType);
        $squareMetre   = Yii::$app->request->post('square_metre', ''); // 面积筛选
        $squareMetre   = explode(',', $squareMetre);

        $saleStatus    = Yii::$app->request->post('sale_status', ''); // 销售状态筛选
        $saleStatus    = explode(',', $saleStatus);

        $openTime      = Yii::$app->request->post('open_time', ''); // 标签筛选
        $openTime      = explode(',', $openTime);

        $label         = Yii::$app->request->post('label', ''); // 标签筛选
        $label         = explode(',', $label);

        // 单价最大最小值
        if ($unitPriceKey)
        {
            $unitPriceMin = Yii::$app->params['unit_price'][$unitPriceKey]['min'];
            $unitPriceMax = Yii::$app->params['unit_price'][$unitPriceKey]['max'];
        }
        // 总价最大最小值
        if ($totalPriceKey)
        {
            $totalPriceMin = Yii::$app->params['total_price'][$totalPriceKey]['min'];
            $totalPriceMax = Yii::$app->params['total_price'][$totalPriceKey]['max'];
        }
        // 面积最大最小值
        if ($squareMetre[0])
        {
            $tmpArr = [];
            foreach ($squareMetre as $v)
            {
                $tmpArr[] = Yii::$app->params['square_metre'][$v]['min'];
                $tmpArr[] = Yii::$app->params['square_metre'][$v]['max'];
            }

            $squareMetreMin = min($tmpArr);
            $squareMetreMax = max($tmpArr);
        }

        // 开盘时间最大最小值
        if ($openTime[0])
        {
            $tmpArr = [];
            foreach ($openTime as $v)
            {
                $tmpArr[] = Yii::$app->params['open_time'][$v]['min'];
                $tmpArr[] = Yii::$app->params['open_time'][$v]['max'];
            }

            $openTimeMin = min($tmpArr);
            $openTimeMax = max($tmpArr);
        }

        // 排序
        if (in_array($sortKey, ['price_avg', 'open_time']) && in_array($sortVal, ['desc', 'asc']))
        {
            $sort = 'a.' . $sortKey . ' ' . $sortVal;
        }

        $query = Properties::find()
            ->alias('a')
            ->select([
                'd.region_code', 'd.lng_lat', 'COUNT(DISTINCT a.properties_id) AS count','d.region_name'
            ])
            ->leftJoin(PropertiesLabelRelation::tableName() . ' b', 'a.properties_id=b.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' c', 'c.properties_label_id=b.properties_label_id')
            ->leftJoin(Region::tableName() . ' d', 'd.region_code=a.region_code')
            ->leftJoin(HouseType::tableName() . ' e', 'e.properties_id=a.properties_id');

        $query->andFilterWhere(['a.city_code' => $cityCode]);
        $query->andFilterWhere(['a.region_code' => $regionCode]);
        $query->andFilterWhere(['>','a.price_metre', $unitPriceMin]);
        $query->andFilterWhere(['<=', 'a.price_metre', $unitPriceMax]);
        $query->andFilterWhere(['>', 'a.price_total_min', $totalPriceMin]);
        $query->andFilterWhere(['<=', 'a.price_total_max', $totalPriceMax]);
        $query->andFilterWhere(['>', 'e.square_metre', $squareMetreMin]);
        $query->andFilterWhere(['<=', 'e.square_metre', $squareMetreMax]);
        $query->andFilterWhere(['>', 'a.open_time', $openTimeMin]);
        $query->andFilterWhere(['<=', 'a.open_time', $openTimeMax]);
        $query->andFilterWhere(['e.room_category_id' => $houseType]);
        $query->andFilterWhere(['a.high_remuneration' => $highRemuneration]);
        $query->andFilterWhere(['a.down_payment_id' => $downPayment]);
        $query->andFilterWhere(['a.fast_get_remuneration' => $fast_get_remuneration]);
        // 物业类型单选或多选判断来筛选
        if (isset($propertyType[1]))
        {
            $query->andFilterWhere(['in', 'a.property_type_id', $propertyType]);
        }
        elseif($propertyType[0] && !isset($propertyType))
        {
            $query->andFilterWhere(['a.property_type_id' => $propertyType[0]]);
        }

        // 销售状态单选或多选判断来筛选
        if(isset($saleStatus[1]))
        {
            $query->andFilterWhere(['in', 'a.sale_status', $saleStatus]);
        }
        elseif($saleStatus[0] && !isset($saleStatus[1]))
        {
            $query->andFilterWhere(['a.sale_status' => $saleStatus[0]]);
        }

        // 标签单选或多选判断来筛选
        if(isset($label[1]))
        {
            $query->andFilterWhere(['in', 'c.properties_label_id', $label]);
        }
        elseif($label[0] && !isset($label[1]))
        {
            $query->andFilterWhere(['c.properties_label_id' => $saleStatus[0]]);
        }


        $model = $query->groupBy('d.region_code')
            ->orderBy($sort)
            ->asArray()
            ->all();

        return response($model);
    }

    /**
     * 全部楼盘列表（带筛选）
     * @return array
     */
    public function actionAll()
    {
        $data           = [];
        $unitPriceMin   = ''; // 单价最小值
        $unitPriceMax   = ''; // 单价最大值
        $totalPriceMin  = ''; // 总价最小值
        $totalPriceMax  = ''; // 总价最大值
        $squareMetreMin = ''; // 面积最小值
        $squareMetreMax = ''; // 面积最大值
        $openTimeMin    = ''; // 开盘时间最小值
        $openTimeMax    = ''; // 开盘时间最大值

        $sort = 'a.properties_id DESC'; // 默认排序

        $page          = Yii::$app->request->post('page', 1); // 页码
        $cityCode          = Yii::$app->request->post('city_code', 110100); // 定位的城市码
        $sortKey       = Yii::$app->request->post('sort_key', ''); // 排序键
        $sortVal       = Yii::$app->request->post('sort_val', ''); // 排序值
        $regionCode    = Yii::$app->request->post('region_code', ''); // 区码 城市筛选
        $unitPriceKey  = Yii::$app->request->post('unit_price', ''); // 单价筛选
        $totalPriceKey = Yii::$app->request->post('total_price', ''); // 总价筛选
        $houseType     = Yii::$app->request->post('house_type', ''); // 户型筛选


        $highRemuneration = Yii::$app->request->post('high_remuneration', ''); // 高佣金筛选
        $downPayment      = Yii::$app->request->post('down_payment', ''); // 首付筛选
        $fast_get_remuneration      = Yii::$app->request->post('fast_get_remuneration', ''); // 快速拿佣筛选

        $propertyType  = Yii::$app->request->post('property_type_id', ''); // 物业类型筛选
        $propertyType  = explode(',', $propertyType);
        $squareMetre   = Yii::$app->request->post('square_metre', ''); // 面积筛选
        $squareMetre   = explode(',', $squareMetre);

        $saleStatus    = Yii::$app->request->post('sale_status', ''); // 销售状态筛选
        $saleStatus    = explode(',', $saleStatus);

        $openTime      = Yii::$app->request->post('open_time', ''); // 标签筛选
        $openTime      = explode(',', $openTime);

        $label         = Yii::$app->request->post('label', ''); // 标签筛选
        $label         = explode(',', $label);

        // 单价最大最小值
        if ($unitPriceKey)
        {
            $unitPriceMin = Yii::$app->params['unit_price'][$unitPriceKey]['min'];
            $unitPriceMax = Yii::$app->params['unit_price'][$unitPriceKey]['max'];
        }
        // 总价最大最小值
        if ($totalPriceKey)
        {
            $totalPriceMin = Yii::$app->params['total_price'][$totalPriceKey]['min'];
            $totalPriceMax = Yii::$app->params['total_price'][$totalPriceKey]['max'];
        }
        // 面积最大最小值
        if ($squareMetre[0])
        {
            $tmpArr = [];
            foreach ($squareMetre as $v)
            {
                $tmpArr[] = Yii::$app->params['square_metre'][$v]['min'];
                $tmpArr[] = Yii::$app->params['square_metre'][$v]['max'];
            }

            $squareMetreMin = min($tmpArr);
            $squareMetreMax = max($tmpArr);
        }

        // 开盘时间最大最小值
        if ($openTime[0])
        {
            $tmpArr = [];
            foreach ($openTime as $v)
            {
                $tmpArr[] = Yii::$app->params['open_time'][$v]['min'];
                $tmpArr[] = Yii::$app->params['open_time'][$v]['max'];
            }

            $openTimeMin = min($tmpArr);
            $openTimeMax = max($tmpArr);
        }

        // 排序
        if (in_array($sortKey, ['price_avg', 'open_time']) && in_array($sortVal, ['desc', 'asc']))
        {
            $sort = 'a.' . $sortKey . ' ' . $sortVal;
        }

        $offset = ($page - 1) * \Yii::$app->params['pageSize'];
        $query = Properties::find()
            ->alias('a')
            ->select([
                'a.properties_id','a.name','a.pic','a.price_metre','a.sale_status',
                'MIN(e.square_metre) AS square_metre_min', 'MAX(e.square_metre) AS square_metre_max',
                'GROUP_CONCAT(DISTINCT c.label_name) AS label_name','MAX(d.region_name) AS region_name'
            ])
            ->leftJoin(PropertiesLabelRelation::tableName() . ' b', 'a.properties_id=b.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' c', 'c.properties_label_id=b.properties_label_id')
            ->leftJoin(Region::tableName() . ' d', 'd.region_code=a.region_code')
            ->leftJoin(HouseType::tableName() . ' e', 'e.properties_id=a.properties_id');

        $query->andFilterWhere(['a.city_code' => $cityCode]);
        $query->andFilterWhere(['a.region_code' => $regionCode]);
        $query->andFilterWhere(['>','a.price_metre', $unitPriceMin]);
        $query->andFilterWhere(['<=', 'a.price_metre', $unitPriceMax]);
        $query->andFilterWhere(['>', 'a.price_total_min', $totalPriceMin]);
        $query->andFilterWhere(['<=', 'a.price_total_max', $totalPriceMax]);
        $query->andFilterWhere(['>', 'e.square_metre', $squareMetreMin]);
        $query->andFilterWhere(['<=', 'e.square_metre', $squareMetreMax]);
        $query->andFilterWhere(['>', 'a.open_time', $openTimeMin]);
        $query->andFilterWhere(['<=', 'a.open_time', $openTimeMax]);
        $query->andFilterWhere(['e.room_category_id' => $houseType]);
        $query->andFilterWhere(['a.high_remuneration' => $highRemuneration]);
        $query->andFilterWhere(['a.down_payment_id' => $downPayment]);
        $query->andFilterWhere(['a.fast_get_remuneration' => $fast_get_remuneration]);

        // 物业类型单选或多选判断来筛选
        if (isset($propertyType[1]))
        {
            $query->andFilterWhere(['in', 'a.property_type_id', $propertyType]);
        }
        elseif($propertyType[0] && !isset($propertyType))
        {
            $query->andFilterWhere(['a.property_type_id' => $propertyType[0]]);
        }

        // 销售状态单选或多选判断来筛选
        if(isset($saleStatus[1]))
        {
            $query->andFilterWhere(['in', 'a.sale_status', $saleStatus]);
        }
        elseif($saleStatus[0] && !isset($saleStatus[1]))
        {
            $query->andFilterWhere(['a.sale_status' => $saleStatus[0]]);
        }

        // 标签单选或多选判断来筛选
        if(isset($label[1]))
        {
            $query->andFilterWhere(['in', 'c.properties_label_id', $label]);
        }
        elseif($label[0] && !isset($label[1]))
        {
            $query->andFilterWhere(['c.properties_label_id' => $saleStatus[0]]);
        }


        $model = $query->groupBy('a.properties_id')
            ->orderBy($sort)
            ->offset($offset)
            ->limit(\Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['sale_status'] = \Yii::$app->params['sale_status'][$v['sale_status']];
            $v['square_metre_min'] = floatval($v['square_metre_min']);
            $v['square_metre_max'] = floatval($v['square_metre_max']);
            if ($v['pic'])
            {
                $v['pic'] = json_decode($v['pic']);
            }
            $data[$k] = $v;
        }

        return response($data);
    }

    /**
     * 楼盘详情
     * @param int $id
     * @return array
     */
    public function actionInfo()
    {
        $data = [];
        $propertiesId = Yii::$app->request->get('properties_id', 0);
        if (!$propertiesId)
        {
            return response([], 10001);
        }
        $model = Properties::find()
            ->alias('a')
            ->select([
                'a.properties_id','a.name','a.pic','a.video','a.region_code','a.sale_status',
                'a.open_time','a.address','a.price_avg','a.price_total_min','a.price_total_max','a.property_type_id',
                'GROUP_CONCAT(c.label_name) AS label_name'
            ])
            ->leftJoin(PropertiesLabelRelation::tableName() . ' b', 'a.properties_id=b.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' c', 'c.properties_label_id=b.properties_label_id')
            ->where(['a.properties_id' => $propertiesId])
            ->groupBy('a.properties_id')
            ->asArray()
            ->one();
        if ($model['pic'])
        {
            $model['pic'] = json_decode($model['pic']);
        }

        $model['property_type_name'] = Yii::$app->params['property_type_name'][$model['property_type_id']];
        $model['sale_status'] = Yii::$app->params['sale_status'][$model['sale_status']];
        $model['open_time'] = date('Y-m-d');
        $data['info'] = $model;

        // 优惠活动
        $activity = GroupBuying::find()
            ->alias('a')
            ->select([
                'a.group_buying_id','b.title', 'b.people', 'b.remark'
            ])
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->where(['a.properties_id' => $propertiesId, 'status' => '0'])
            ->andWhere(['>', 'b.e_time', time()])
            ->orderBy('a.group_buying_id DESC')
            ->limit(2)
            ->asArray()
            ->all();
        $data['activity']['data'] = $activity;
        $data['activity']['count'] = GroupBuying::find()->where(['properties_id' => $propertiesId])->count(1);

        // 楼盘资讯
        $information = PropertiesInformation::find()
            ->select([
                'properties_information_id','title','pic','create_time'
            ])
            ->where(['properties_id' => $propertiesId])
            ->orderBy('properties_information_id DESC')
            ->limit(2)
            ->asArray()
            ->all();
        if ($information)
        {
            foreach ($information as $k => $v)
            {
                $information[$k]['create_time'] = date('n-d H:i:s', $v['create_time'] );
                $information[$k]['title'] = '【' . $model['name'] . '】' . $v['title'];
            }

        }
        $data['information'] = $information;

        // 户型图
        $houseType = HouseType::find()
            ->select([
                'house_type_id','pic','square_metre','room_category_id'
            ])
            ->where(['properties_id' => $propertiesId])
            ->asArray()
            ->limit(4)
            ->all();
        $data['houseType'] = $houseType;

        /*// 任务
        $task = Task::find()
            ->select(['task_id', 'title', 'type'])
            ->asArray()
            ->all();
        if ($task)
        {
            foreach ($task as $k => $v)
            {
                $v['type_title'] = Yii::$app->params['task_type'][$v['type']];
                $task[$k] = $v;
            }

        }
        $data['task'] = $task;*/

        // 点评
        $comment = Comment::find()
            ->alias('a')
            ->select([
                'a.comment_id','a.user_id','a.content','a.create_time','b.headimgurl','b.nickname','b.is_adviser'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id=b.user_id')
            ->where(['a.properties_id' => $propertiesId])
            ->asArray()
            ->one();
        if ($comment)
        {
            $comment['create_time'] = date('n-d H:i:s', $comment['create_time']);
        }
        $data['comment'] = $comment;

        // 问答
        $ask = PropertiesAsk::find()
            ->alias('a')
            ->select([
                'a.properties_ask_id','a.title','a.create_time','b.content'
            ])
            ->leftJoin(PropertiesAnswers::tableName() . ' b', 'a.properties_ask_id=b.properties_ask_id')
            ->where(['a.properties_id' => $propertiesId])
            ->asArray()
            ->one();
        if ($ask)
        {
            $ask['create_time'] = date('n-d H:i:s', $ask['create_time']);
        }
        $data['ask'] = $ask;

        // 顾问
        $adviser = PropertiesAdviserRelation::find()
            ->alias('a')
            ->select([
                'b.user_id','b.headimgurl','b.nickname','b.phone','b.is_adviser'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id=b.user_id')
            ->where(['a.properties_id' => $propertiesId])
            ->limit(3)
            ->asArray()
            ->all();

        $data['adviser'] = $adviser;

        // 猜你喜欢
        $guess = Properties::find()
            ->alias('a')
            ->select([
                'a.properties_id','a.name','a.pic','a.price_metre','a.sale_status',
                'GROUP_CONCAT(c.label_name) AS label_name','MAX(d.region_name) AS region_name'
            ])
            ->leftJoin(PropertiesLabelRelation::tableName() . ' b', 'a.properties_id=b.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' c', 'c.properties_label_id=b.properties_label_id')
            ->leftJoin(Region::tableName() . ' d', 'd.region_code=a.region_code')
            ->groupBy('a.properties_id')
            ->limit(3)
            ->asArray()
            ->all();

        foreach ($guess as $k => $v){
            if ($v['pic'])
            {
                $v['pic'] = json_decode($v['pic']);
                $guess[$k] = $v;
            }
        }

        $data['guess'] = $guess;

        return response($data);
    }
}
