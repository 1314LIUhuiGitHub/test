<?php


namespace app\common;


class Tree
{
    /**
     * 生成菜单树
     *
     * @param array $menuList
     * @return array
     */
    public static function getLeftMenuTree(array $menuList): array
    {
        $map = [];
        $menuTree = [];
        $pid = "parent_id";
        foreach ($menuList as &$it) {
            $map[$it['menu_id']] = &$it;
            if (!$it['component']) {
                $it['component'] = 'Layout';
            }
            $it['meta'] = ['title' => $it['menu_name'], 'icon' => $it['icon']];
            $it['name'] = ucwords($it['path']);
            if ($it['parent_id'] == 0) {
                $it['redirect'] = 'noRedirect';
                $it['alwaysShow'] = true;
                $it['path'] = '/' . $it['path'];
            }
            if ($it['is_frame'] == 0 ){
                unset($it['redirect']);
                unset($it['alwaysShow']);
            }
            unset($it['is_frame']);
            unset($it['menu_name']);
            unset($it['icon']);
        }
        //数据的ID名生成新的引用索引树
        foreach ($menuList as &$at) {
            $parent = &$map[$at[$pid]];
            if ($parent) {
                $parent['children'][] = &$at;
            } else {
                $menuTree[] = &$at;
            }
            unset($at['menu_id']);
            unset($at['parent_id']);
        }
        return $menuTree;
    }

    /**
     * 获取菜单结构树
     *
     * @param array $treeList
     * @return array
     */
    public static function getRoleMenu(array $treeList): array
    {
        $map = [];
        $menuTree = [];
        $pid = "parent_id";
        foreach ($treeList as &$it) {
            $it["id"] = $it['menu_id'];
            $it["label"] = $it['menu_name'];
            $map[$it['menu_id']] = &$it;
        }
        foreach ($treeList as &$at) {
            $parent = &$map[$at[$pid]];
            if ($parent) {
                $parent['children'][] = &$at;
            } else {
                $menuTree[] = &$at;
            }
        }
        return $menuTree;
    }

    /**
     * 菜单全字段树
     *
     * @param array $treeList
     * @return array
     */
    public static function menuTree(array $treeList):array
    {
        $map = [];
        $menuTree = [];
        $pid = "parent_id";
        foreach ($treeList as &$it) {
            $map[$it['menu_id']] = &$it;
        }
        foreach ($treeList as &$at) {
            $parent = &$map[$at[$pid]];
            if ($parent) {
                $parent['children'][] = &$at;
            } else {
                $menuTree[] = &$at;
            }
        }
        return $menuTree;

    }
    /**
     * 获取短部门结构树
     *
     * @param array $treeList
     * @return array
     */
    public static function getDeptTree(array $treeList): array
    {
        $map = [];
        $deptTree = [];
        $pid = "parent_id";
        foreach ($treeList as &$it) {
            $it["id"] = $it['dept_id'];
            $it["label"] = $it['dept_name'];
            $map[$it['dept_id']] = &$it;
        }
        foreach ($treeList as &$at) {
            $parent = &$map[$at[$pid]];
            if ($parent) {
                $parent['children'][] = &$at;
            } else {
                $deptTree[] = &$at;
            }
        }
        return $deptTree;
    }

    /**
     * 全字段部门结构树
     *
     * @param array $deptList
     * @return array
     */
    public static function deptTree(array $deptList): array
    {
        if (empty($deptList)){
            return [];
        }
        $maps = [];
        $deptTrees = [];
        $pid = "parent_id";
        foreach ($deptList as &$it) {
            $maps[$it['dept_id']] = &$it;
        }
        foreach ($deptList as &$at) {
            $parents = &$maps[$at[$pid]];
            if ($parents) {
                $parents['children'][] = &$at;
            } else {
                $deptTrees[] = &$at;
            }
        }
        return $deptTrees;
    }

}
