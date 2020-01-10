<?php

namespace api\modules\v1\controllers;

use api\controllers\BaseCotroller;


class ArticleController extends BaseCotroller
{

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        return [1,2,3];
    }

    public function actionTest()
    {
        return [1,2,3];
    }

}
