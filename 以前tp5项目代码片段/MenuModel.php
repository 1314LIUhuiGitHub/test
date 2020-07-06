<?php


namespace app\admin\model;


use app\common\Tree;
use think\Model;


class MenuModel extends Model
{
    protected $table = "sys_menu";
    protected $pk = "menu_id";
    /**
     * 获取全部菜单
     *
     * @param string $field
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenu(string $field, array $where = []): array
    {
        $where[] = ["visible", "=", 0];
        $where[] = ["menu_type", "IN", ['M', 'C']];
        $menuList = $this->field($field)
            ->where($where)
            ->order('order_num', 'ASC')
            ->select()->toArray();
        return Tree::getLeftMenuTree($menuList);
    }

    /**
     * 获取菜单树结构(包括按钮)
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAddRoleMenus(): array
    {
        $menuList = $this->field("menu_id, parent_id, menu_name")
            ->where(["visible" => 0])
            ->order('order_num', 'ASC')
            ->select()->toArray();
        //使用传值引用获取结构树
        return Tree::getRoleMenu($menuList);
    }

    /**
     * 获取菜单树结构(包括按钮) 全字段
     *
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where): array
    {
        $menuList = $this->field([
            'menu_id',
            'parent_id',
            'menu_name',
            'component',
            'icon',
            'order_num',
            'visible',
            'is_frame',
            'perms',
            'create_time',
            'menu_type',
            'path',
        ])->where($where)->order('order_num', 'ASC')->select()->toArray();
        //使用传值引用获取结构树
        return Tree::menuTree($menuList);
    }

    /**
     * 根据ID获取一条数据
     *
     * @param int $menuId
     * @return array|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOneMenu(int $menuId)
    {
        $data = $this->field("
            menu_id as menuId,
            parent_id as parentId,
            menu_name as menuName,
            component,
            icon,
            order_num as orderNum,
            visible,
            is_frame,
            perms,
            path,
            create_time as createTime,
            menu_type as menuType
        ")->where(["menu_id" => $menuId])->find();
        if ($data) {
            $data["isFrame"] = (string)$data["is_frame"];
        }
        return $data ? $data : [];
    }
}
