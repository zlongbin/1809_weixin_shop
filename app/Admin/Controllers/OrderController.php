<?php

namespace App\Admin\Controllers;

use App\Model\OrderModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderModel);

        $grid->id('Oid');
        $grid->order_sn('订单号');
        $grid->uid('用户id');
        $grid->order_amount('订单金额');
        $grid->pay_amount('支付金额');
        $grid->add_time('添加时间');
        $grid->pay_time('支付时间');
        $grid->status('Status');
        $grid->is_delete('Is delete');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(OrderModel::findOrFail($id));

        $show->id('Oid');
        $show->order_sn('Order sn');
        $show->uid('Uid');
        $show->order_amount('Order amount');
        $show->pay_amount('Pay amount');
        $show->add_time('Add time');
        $show->pay_time('Pay time');
        $show->status('Status');
        $show->is_delete('Is delete');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new OrderModel);

        $form->number('id', 'Oid');
        $form->text('order_sn', 'Order sn');
        $form->number('uid', 'Uid');
        $form->number('order_amount', 'Order amount');
        $form->number('pay_amount', 'Pay amount');
        $form->number('add_time', 'Add time');
        $form->number('pay_time', 'Pay time');
        $form->number('status', 'Status');
        $form->number('is_delete', 'Is delete');

        return $form;
    }
    // php artisan admin:make MediaController --model=App\\Model\\WxUserModel
}
