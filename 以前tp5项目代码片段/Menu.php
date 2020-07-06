<?php


namespace app\admin\controller\system;


use app\admin\controller\AdminBase;
use app\admin\model\MenuModel;
use app\admin\model\RoleMenuModel;
use app\admin\model\RoleModel;
use app\validate\MenuValidate;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;

class Menu extends AdminBase
{
    /**
     * 菜单列表
     *
     * @throws \Throwable
     */
    public function index()
    {
        $menuName = $this->request->param("menuName");
        $status = $this->request->param("status");
        $where = [];
        if ($status) {
            $where['status'] = $status;
        }
        if ($menuName) {
            $where[] = ['menu_name','like','%' . $menuName . '%'];
        }
        $menu = new MenuModel();
        $data["data"] = $menu->getList($where);
        return $this->success($data);
    }

    /**
     * 获取菜单树结构(包括按钮)
     */
    public function menuTree()
    {
        $menu = new MenuModel();
        $treeList = Cache::get("addRoleMenu");
        if ($treeList == null) {
            $treeList = $menu->getAddRoleMenus();
            Cache::set("addRoleMenu", json_encode($treeList), 86400);
        } else {
            $treeList = json_decode($treeList, true);
        }
        $data["data"] = $treeList;
        return $this->success($data);
    }

    /**
     * 角色已经选择中的菜单ids
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function selectRoleMenu()
    {
        $roleId = $this->request->param("roleId");
        $menus = Cache::get("selectRoleMenu=" . $roleId);
        if ($menus == null) {
            $roleMenu = new RoleMenuModel();
            $menu = new MenuModel();
            $menuIds = $roleMenu->getRoleMenuId($roleId);
            $menus = [];
            foreach ($menuIds as $val) {
                if ($menu->where(["parent_id" => $val["menu_id"]])->count() < 1) {
                    $menus[] = $val["menu_id"];
                }
            }
            Cache::set("selectRoleMenu=" . $roleId, json_encode($menus), 1086400);
        } else {
            $menus = json_decode($menus, true);
        }
        $data["data"] = $menus;
        return $this->success($data);
    }

    /**
     * 新增/修改 菜单
     *
     * @return \think\response\Json
     */
    public function save()
    {
        $param = $this->request->param();
        $param["parentId"] = empty($param["parentId"]) ? 0 : $param["parentId"];
        $v = $this->formCheck(new MenuValidate(), "save", $param);
        if ($v["res"] === false) {
            return $this->error($v["msg"]);
        }
        try {
            Db::startTrans();
            $where = [
                ['menu_name', '=', $param["menuName"]],
                ['parent_id', '=', $param["parentId"]],
                ['menu_type', '=', $param["menuType"]],
            ];
            if (empty($param["menuId"])) {
                $menu = new MenuModel();
                $isExists = MenuModel::where($where)->find();
                if (!empty($isExists)) {
                    throw new Exception("菜单已存在!");
                }
                $menu->create_time = date("Y-m-d H:i:s");
                $menu->create_by = $this->token["login_user_name"];
            } else {
                $menu = MenuModel::find($param["menuId"]);
                $where[] = ['menu_id', '<>', $param["menuId"]];
                $isExists = MenuModel::where($where)->find();
                if (!empty($isExists)) {
                    throw new Exception("菜单已存在!");
                }
                $menu->update_time = date("Y-m-d H:i:s");
                $menu->update_by = $this->token["login_user_name"];
            }
            //删除缓存
            Cache::delete("addRoleMenu");
            Cache::delete("menuTree=all");
            $roles = RoleModel::field(["role_id"])->select();
            foreach ($roles as $roleId) {
                Cache::delete("menuTree=" . $roleId["role_id"]);
            }
            $menu->menu_name = $param["menuName"];
            $menu->order_num = $param["orderNum"];
            $menu->menu_type = $param["menuType"];
            $menu->is_frame = $param["isFrame"];
            $menu->visible = $param["visible"];
            $menu->parent_id = $param["parentId"];
            switch ($param["menuType"]) {
                case "M": //M目录
                {
                    $menu->icon = $param["icon"];
                    $menu->visible = $param["visible"];
                    $menu->path = $param["path"];
                    break;
                }
                case "C"://C菜单
                {
                    $menu->icon = $param["icon"];
                    $menu->visible = $param["visible"];
                    $menu->perms = $param["perms"];
                    $menu->component = $param["component"];
                    $menu->path = $param["path"];
                    break;
                }
                case "F"://F按钮
                {
                    $menu->perms = $param["perms"];
                    break;
                }
                default:
                {
                    throw new \Exception("菜单类型错误");
                    break;
                }
            }
            if (empty($menuId)) {
                $menuId = $menu->save();
                if ($menuId === null) {
                    throw new Exception("操作失败!");
                }
            } else {
                $menu->save();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->error($e->getMessage());
        }
        return $this->success();
    }

    /**
     * 菜单详情
     *
     * @return \think\response\Json
     */
    public function detail()
    {
        $menuId = $this->request->param("menuId");
        $menu = new MenuModel();
        $data["data"] = $menu->getOneMenu($menuId);
        return $this->success($data);
    }

    /**
     * 删除菜单
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function del()
    {
        $menuId = $this->request->param("menuId");
        $res = MenuModel::destroy($menuId);
        if ($res){
            //删除缓存
            Cache::delete("addRoleMenu");
            Cache::delete("menuTree=all");
            $roles = RoleModel::field(["role_id"])->select();
            foreach ($roles as $roleId) {
                Cache::delete("menuTree=" . $roleId["role_id"]);
            }
        }else{
            return $this->error();
        }
        return $this->success();
    }

}
