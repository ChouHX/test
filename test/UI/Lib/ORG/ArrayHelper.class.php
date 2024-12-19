<?php
// $Id: array.php 2630 2009-07-17 16:43:52Z jerry $
/**
 * ���� Helper_Array ��
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: array.php 2630 2009-07-17 16:43:52Z jerry $
 * @package helper
 */
/**
 * Helper_Array ���ṩ��һ�����������ķ��� 
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: array.php 2630 2009-07-17 16:43:52Z jerry $
 * @package helper
 */
class ArrayHelper
{
    /**
     * ��������ɾ���հ׵�Ԫ�أ�����ֻ�пհ��ַ���Ԫ�أ�
     *
     * �÷���
     * @code php
     * $arr = array('', 'test', '   ');
     * Helper_Array::removeEmpty($arr);
     *
     * dump($arr);
     *   // �������н�ֻ�� 'test'
     * @endcode
     *
     * @param array $arr Ҫ���������
     * @param boolean $trim �Ƿ������Ԫ�ص��� trim ����
     */
    static function removeEmpty(& $arr, $trim = true)
    {
        foreach ($arr as $key => $value) 
        {
            if (is_array($value)) 
            {
                self::removeEmpty($arr[$key]);
            } 
            else 
            {
                $value = trim($value);
                if ($value == '') 
                {
                    unset($arr[$key]);
                } 
                elseif ($trim) 
                {
                    $arr[$key] = $value;
                }
            }
        }
    }
    /**
     * ��һ����ά�����з���ָ����������ֵ
     *
     * �÷���
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $values = Helper_Array::cols($rows, 'value');
     *
     * dump($values);
     *   // ������Ϊ
     *   // array(
     *   //   '1-1',
     *   //   '2-1',
     *   // )
     * @endcode
     *
     * @param array $arr ����Դ
     * @param string $col Ҫ��ѯ�ļ�
     *
     * @return array ����ָ��������ֵ������
     */
    static function getCols($arr, $col)
    {
        $ret = array();
        foreach ($arr as $row) 
        {
            if (isset($row[$col])) { $ret[] = $row[$col]; }
        }
        return $ret;
    }
    /**
     * ��һ����ά����ת��Ϊ HashMap�������ؽ��
     *
     * �÷�1��
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = Helper_Array::hashMap($rows, 'id', 'value');
     *
     * dump($hashmap);
     *   // ������Ϊ
     *   // array(
     *   //   1 => '1-1',
     *   //   2 => '2-1',
     *   // )
     * @endcode
     *
     * ���ʡ�� $value_field ��������ת�����ÿһ��Ϊ���������������ݵ����顣
     *
     * �÷�2��
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = Helper_Array::hashMap($rows, 'id');
     *
     * dump($hashmap);
     *   // ������Ϊ
     *   // array(
     *   //   1 => array('id' => 1, 'value' => '1-1'),
     *   //   2 => array('id' => 2, 'value' => '2-1'),
     *   // )
     * @endcode
     *
     * @param array $arr ����Դ
     * @param string $key_field ����ʲô����ֵ����ת��
     * @param string $value_field ��Ӧ�ļ�ֵ
     *
     * @return array ת����� HashMap ��ʽ����
     */
    static function toHashmap($arr, $key_field, $value_field = null)
    {
        $ret = array();
        if ($value_field) 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row[$value_field];
            }
        } 
        else 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row;
            }
        }
        return $ret;
    }
    /**
     * ��һ����ά���鰴��ָ���ֶε�ֵ����
     *
     * �÷���
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     * $values = Helper_Array::groupBy($rows, 'parent');
     *
     * dump($values);
     *   // ���� parent �����������Ϊ
     *   // array(
     *   //   1 => array(
     *   //        array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *   //        array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *   //        array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *   //   ),
     *   //   2 => array(
     *   //        array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *   //        array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *   //   ),
     *   //   3 => array(
     *   //        array('id' => 6, 'value' => '6-1', 'parent' => 3),
     *   //   ),
     *   // )
     * @endcode
     *
     * @param array $arr ����Դ
     * @param string $key_field ��Ϊ�������ݵļ���
     *
     * @return array �����Ľ��
     */
    static function groupBy($arr, $key_field)
    {
        $ret = array();
        foreach ($arr as $row) 
        {
            $key = $row[$key_field];
            $ret[$key][] = $row;
        }
        return $ret;
    }
    /**
     * ��һ��ƽ��Ķ�ά���鰴��ָ�����ֶ�ת��Ϊ��״�ṹ
     *
     * �÷���
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 0),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 0),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 0),
     *
     *     array('id' => 7, 'value' => '2-1-1', 'parent' => 2),
     *     array('id' => 8, 'value' => '2-1-2', 'parent' => 2),
     *     array('id' => 9, 'value' => '3-1-1', 'parent' => 3),
     *     array('id' => 10, 'value' => '3-1-1-1', 'parent' => 9),
     * );
     *
     * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes');
     *
     * dump($tree);
     *   // ������Ϊ��
     *   // array(
     *   //   array('id' => 1, ..., 'nodes' => array()),
     *   //   array('id' => 2, ..., 'nodes' => array(
     *   //        array(..., 'parent' => 2, 'nodes' => array()),
     *   //        array(..., 'parent' => 2, 'nodes' => array()),
     *   //   ),
     *   //   array('id' => 3, ..., 'nodes' => array(
     *   //        array('id' => 9, ..., 'parent' => 3, 'nodes' => array(
     *   //             array(..., , 'parent' => 9, 'nodes' => array(),
     *   //        ),
     *   //   ),
     *   // )
     * @endcode
     *
     * ���Ҫ�������ڵ�Ϊ��������������ʹ�� $refs ������
     * @code php
     * $refs = null;
     * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes', $refs);
     * 
     * // ��� id Ϊ 3 �Ľڵ㼰�������ӽڵ�
     * $id = 3;
     * dump($refs[$id]);
     * @endcode
     *
     * @param array $arr ����Դ
     * @param string $key_node_id �ڵ�ID�ֶ���
     * @param string $key_parent_id �ڵ㸸ID�ֶ���
     * @param string $key_children �����ӽڵ���ֶ���
     * @param boolean $refs �Ƿ��ڷ��ؽ���а����ڵ�����
     *
     * return array ���νṹ������
     */
    static function toTree($arr, $key_node_id, $key_parent_id = 'parent_id',
                           $key_children = 'children', & $refs = null)
    {
        $refs = array();
        foreach ($arr as $offset => $row) 
        {
            $arr[$offset][$key_children] = array();
            $refs[$row[$key_node_id]] =& $arr[$offset];
        }
        $tree = array();
        foreach ($arr as $offset => $row) 
        {
            $parent_id = $row[$key_parent_id];
            if ($parent_id)
            {
                if (!isset($refs[$parent_id]))
                {
                    $tree[] =& $arr[$offset];
                    continue;
                }
                $parent =& $refs[$parent_id];
                $parent[$key_children][] =& $arr[$offset];
            }
            else
            {
                $tree[] =& $arr[$offset];
            }
        }
        return $tree;
    }
    /**
     * ����������չ��Ϊƽ�������
     *
     * ��������� tree() ���������������
     *
     * @param array $tree ��������
     * @param string $key_children �����ӽڵ�ļ���
     *
     * @return array չ���������
     */
    static function treeToArray($tree, $key_children = 'children')
    {
        $ret = array();
        if (isset($tree[$key_children]) && is_array($tree[$key_children]))
        {
            $children = $tree[$key_children];
            unset($tree[$key_children]);
            $ret[] = $tree;
            foreach ($children as $node) 
            {
                $ret = array_merge($ret, self::treeToArray($node, $key_children));
            }
        }
        else
        {
            unset($tree[$key_children]);
            $ret[] = $tree;
        }
        return $ret;
    }
    /**
     * ����ָ���ļ�����������
     *
     * �÷���
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     *
     * $rows = Helper_Array::sortByCol($rows, 'id', SORT_DESC);
     * dump($rows);
     * // ������Ϊ��
     * // array(
     * //   array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * //   array('id' => 5, 'value' => '5-1', 'parent' => 2),
     * //   array('id' => 4, 'value' => '4-1', 'parent' => 2),
     * //   array('id' => 3, 'value' => '3-1', 'parent' => 1),
     * //   array('id' => 2, 'value' => '2-1', 'parent' => 1),
     * //   array('id' => 1, 'value' => '1-1', 'parent' => 1),
     * // )
     * @endcode
     *
     * @param array $array Ҫ���������
     * @param string $keyname ����ļ�
     * @param int $dir ������
     *
     * @return array ����������
     */
    static function sortByCol($array, $keyname, $dir = SORT_ASC)
    {
        return self::sortByMultiCols($array, array($keyname => $dir));
    }
    /**
     * ��һ����ά���鰴�ն���н����������� SQL ����е� ORDER BY
     *
     * �÷���
     * @code php
     * $rows = Helper_Array::sortByMultiCols($rows, array(
     *     'parent' => SORT_ASC, 
     *     'name' => SORT_DESC,
     * ));
     * @endcode
     *
     * @param array $rowset Ҫ���������
     * @param array $args ����ļ�
     *
     * @return array ����������
     */
    static function sortByMultiCols(&$rowset, $args)
    {
        $sortArray = array();
        $sortRule = '';
        foreach ($args as $sortField => $sortDir) 
        {
            foreach ($rowset as $offset => $row) 
            {
                $sortArray[$sortField][$offset] = $row[$sortField];
            }
            $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir[0] . ', ' . $sortDir[1] . ', ';
        }
        if (empty($sortArray) || empty($sortRule)){
            return $rowset;
        }
        eval('array_multisort(' . $sortRule . '$rowset);');
        // return $rowset;
    }
}