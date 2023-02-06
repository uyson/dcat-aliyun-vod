<?php
namespace Uyson\DcatAdmin\AliyunVod\Repositories;
use AlibabaCloud\SDK\Vod\V20170321\Models\AddCategoryRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\DeleteCategoryRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetCategoriesRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetCategoriesResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\UpdateCategoryRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Show;

class CategoryRepository extends Repository
{
    public function getKeyName()
    {
        return 'CateId';
    }
    public function getCreatedAtColumn()
    {
        return null;
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function isSoftDeletes()
    {
        return false;
    }

    public function get(Grid\Model $model = null)
    {


        $client = app('uyson.aliyun.vod');
        $getCategoriesRequest = new GetCategoriesRequest([]);
        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            /**
             * @var GetCategoriesResponse $res
             */
            $res = $client->getCategoriesWithOptions($getCategoriesRequest, $runtime);

            return $res->body->subCategories->toMap()['Category'] ?? [];
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '点播分类获取失败');
        }
    }

    public function store(\Dcat\Admin\Form $form)
    {
        $client = app('uyson.aliyun.vod');

        // 获取待新增的数据
        $attributes = $form->updates();
        $addCategoryRequest = new AddCategoryRequest([
            'cateName' => $attributes['CateName']
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            $client->addCategoryWithOptions($addCategoryRequest, $runtime);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '添加分类失败');
        }
        return 1;
    }

    public function detail(Show $show)
    {
        return $this->find($show->getKey());
    }

    public function edit(Form $form)
    {
        return $this->find($form->getKey());
    }

    protected function find($id) {
        $client = app('uyson.aliyun.vod');
        $getCategoriesRequest = new GetCategoriesRequest([
            'cateId' => $id
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            /**
             * @var GetCategoriesResponse $res
             */
            $res = $client->getCategoriesWithOptions($getCategoriesRequest, $runtime);
            return $res->body->category->toMap();
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '点播分类获取失败');
        }
    }

    public function updating(Form $form)
    {
        return [];
    }

    public function update(Form $form)
    {
        $id = $form->getKey();
        $attributes = $form->updates();
        $client = app('uyson.aliyun.vod');
        $updateCategoryRequest = new UpdateCategoryRequest([
            "cateId" => $id,
            "cateName" => $attributes['CateName']
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            $client->updateCategoryWithOptions($updateCategoryRequest, $runtime);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '点播分类获取失败');
        }
        return true;
    }

    public function deleting(Form $form)
    {
        return [];
    }

    public function delete(Form $form, array $deletingData)
    {
        $id = $form->getKey();
        $client = app('uyson.aliyun.vod');
        $deleteCategoryRequest = new DeleteCategoryRequest([
            "cateId" => $id
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            $client->deleteCategoryWithOptions($deleteCategoryRequest, $runtime);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '点播分类获取失败');
        }
        return true;
    }

}
