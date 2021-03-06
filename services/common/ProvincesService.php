<?php
namespace services\common;

use Yii;
use common\components\Service;
use common\models\common\Provinces as ProvincesModel;

/**
 * Class Provinces
 * @package common\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ProvincesService extends Service
{
    const CACHE_KEY = 'sys:provinces';

    /**
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     *
     */
    public function getAllByIds($ids)
    {
        return ProvincesModel::find()
            ->select(['id', 'title', 'pid', 'level'])
            ->where(['in', 'id', $ids])
            ->asArray()
            ->all();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        // 获取缓存
        if (!($data = Yii::$app->cache->get(self::CACHE_KEY)))
        {
            // 数据库依赖缓存
            $dependency = new \yii\caching\DbDependency([
                'sql' => ProvincesModel::find()
                    ->select('id')
                    ->orderBy('id desc')
                    ->createCommand()
                    ->getRawSql(),
            ]);

            $data = ProvincesModel::find()
                ->select(['id', 'title', 'pid', 'level'])
                ->where(['<=', 'level', 4])
                ->asArray()
                ->all();

            Yii::$app->cache->set(self::CACHE_KEY, $data, 60 * 60 * 24 * 30, $dependency);
        }

        return $data;
    }
}