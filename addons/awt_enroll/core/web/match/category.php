<?php
/**
 * File  category.php 一级分类
 * Created by https://github.com/junxxx
 * Email: hprjunxxx@gmail.com
 * Author: junxxx
 * Datetime: 2017/8/26 12:34
 */
defined('IN_IA') or exit('Access Denied!');
global $_W, $_GPC;

$tablename = 'enroll_activity_cate';
$uniacid = $_W['uniacid'];

$operation = isset($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == 'display') {
    $where = ' WHERE 1 AND uniacid=:uniacid';
    $params = array(
        ':uniacid' => $uniacid,
    );
    $sql = ' SELECT * FROM ' . tablename($tablename) . $where;
    $orderBy = ' ORDER BY id DESC ';
    $sql .= $orderBy;
    $list = pdo_fetchall($sql, $params);

    if (!empty($list)) {
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
            $row['updatetime'] = date('Y-m-d H:i:s', $row['updatetime']);
        }
        unset($row);
    }

} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);

    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $uniacid,
            'cname' => trim($_GPC['cname']),
        );
        if (!empty($id)) {
            $data['updatetime'] = time();
            pdo_update($tablename, $data, array('id' => $id));
            plog('match.cate.edit', "修改活动分类 ID: {$id}");
        } else {
            $data['createtime'] = time();
            $data['updatetime'] = time();
            pdo_insert($tablename, $data);
            $id = pdo_insertid();
            plog('match.cate.add', "新增活动分类 ID: {$id}");
        }
        message('更新活动分类成功！', $this->createWebUrl('match/category', array('op' => 'display')), 'success');
    }

    $sql = 'SELECT * FROM ' . tablename($tablename) . 'WHERE id = :id AND uniacid = :uniacid LIMIT 1';
    $params = array(
        ':id' => $id,
        ':uniacid' => $uniacid,
    );
    $item = pdo_fetch($sql, $params);

} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $sql = 'SELECT id FROM ' . tablename($tablename) . 'WHERE id = :id AND uniacid = :uniacid LIMIT 1';
        $params = array(
            ':id' => $id,
            ':uniacid' => $uniacid,
        );
        $category = pdo_fetch($sql, $params);
        if (empty($category)) {
            message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('match/category', array('op' => 'display')), 'error');
        }
        $data = array(
            'id' => $id,
            'uniacid' => $uniacid,
        );
        pdo_delete($tablename, $data);
        message('活动分类删除成功！', $this->createWebUrl('match/category', array('op' => 'display')), 'success');
    }
}

load()->func('tpl');
include $this->template('web/match/category');