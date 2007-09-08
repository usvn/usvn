<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TestCommon.php 5906 2007-07-28 02:58:20Z bkarwin $
 */


/**
 * @see Zend_Db_TestSetup
 */
require_once 'Zend/Db/TestSetup.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Select_TestCommon extends Zend_Db_TestSetup
{
    /**
     * Test basic use of the Zend_Db_Select class.
     */
    protected function _select()
    {
        $select = $this->_db->select();
        $select->from('zfproducts');
        return $select;
    }

    public function testSelect()
    {
        $select = $this->_select();
        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $this->_db->query($select);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['product_id']); // correct data
    }

    /**
     * Test basic use of the Zend_Db_Select class.
     */
    public function testSelectQuery()
    {
        $select = $this->_select();
        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $select->query();
        $row = $stmt->fetch();
        $stmt->closeCursor();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['product_id']); // correct data
    }

    /**
     * Test Zend_Db_Select specifying columns
     */
    protected function _selectColumnsScalar()
    {
        $select = $this->_db->select()
            ->from('zfproducts', 'product_name'); // scalar
        return $select;
    }

    public function testSelectColumnsScalar()
    {
        $select = $this->_selectColumnsScalar();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertEquals(1, count($result[0]), 'Expected column count of result set to be 1');
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    protected function _selectColumnsArray()
    {
        $select = $this->_db->select()
            ->from('zfproducts', array('product_id', 'product_name')); // array
        return $select;
    }

    public function testSelectColumnsArray()
    {
        $select = $this->_selectColumnsArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertEquals(2, count($result[0]), 'Expected column count of result set to be 2');
        $this->assertThat($result[0], $this->arrayHasKey('product_id'));
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    /**
     * Test support for column aliases.
     * e.g. from('table', array('alias' => 'col1')).
     */
    protected function _selectColumnsAliases()
    {
        $select = $this->_db->select()
            ->from('zfproducts', array('alias' => 'product_name'));
        return $select;
    }

    public function testSelectColumnsAliases()
    {
        $select = $this->_selectColumnsAliases();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertThat($result[0], $this->arrayHasKey('alias'));
        $this->assertThat($result[0], $this->logicalNot($this->arrayHasKey('product_name')));
    }

    /**
     * Test syntax to support qualified column names,
     * e.g. from('table', array('table.col1', 'table.col2')).
     */
    protected function _selectColumnsQualified()
    {
        $select = $this->_db->select()
            ->from('zfproducts', "zfproducts.product_name");
        return $select;
    }

    public function testSelectColumnsQualified()
    {
        $select = $this->_selectColumnsQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    /**
     * Test support for columns defined by Zend_Db_Expr.
     */
    protected function _selectColumnsExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $select = $this->_db->select()
            ->from('zfproducts', new Zend_Db_Expr($products.'.'.$product_name));
        return $select;
    }

    public function testSelectColumnsExpr()
    {
        $select = $this->_selectColumnsExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    /**
     * Test support for automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. from('table', array('COUNT(*)'))
     * should generate the same result as
     * from('table', array(new Zend_Db_Expr('COUNT(*)')))
     */
    protected function _selectColumnsAutoExpr()
    {
        $select = $this->_db->select()
            ->from('zfproducts', array('count' => 'COUNT(*)'));
        return $select;
    }

    public function testSelectColumnsAutoExpr()
    {
        $select = $this->_selectColumnsAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('count'));
        $this->assertEquals(3, $result[0]['count']);
    }

    /**
     * Test adding the DISTINCT query modifier to a Zend_Db_Select object.
     */
    protected function _selectDistinctModifier()
    {
        $select = $this->_db->select()
            ->distinct()
            ->from('zfproducts', new Zend_Db_Expr(327));
        return $select;
    }

    public function testSelectDistinctModifier()
    {
        $select = $this->_selectDistinctModifier();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     *
    public function testSelectForUpdateModifier()
    {
    }
     */

    /**
     * Test support for schema-qualified table names in from()
     * e.g. from('schema.table').
     */
    protected function _selectFromQualified()
    {
        $schema = $this->_util->getSchema();
        $select = $this->_db->select()
            ->from("$schema.zfproducts");
        return $select;
    }

    public function testSelectFromQualified()
    {
        $select = $this->_selectFromQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */
    protected function _selectJoin()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->join('zfbugs_products', "$products.$product_id = $bugs_products.$product_id");
        return $select;
    }

    public function testSelectJoin()
    {
        $select = $this->_selectJoin();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */
    protected function _selectJoinWithCorrelationName()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');
        $xyz1 = $this->_db->quoteIdentifier('xyz1');
        $xyz2 = $this->_db->quoteIdentifier('xyz2');

        $select = $this->_db->select()
            ->from( array('xyz1' => 'zfproducts') )
            ->join( array('xyz2' => 'zfbugs_products'), "$xyz1.$product_id = $xyz2.$product_id")
            ->where("$xyz1.$product_id = 1");
        return $select;
    }

    public function testSelectJoinWithCorrelationName()
    {
        $select = $this->_selectJoinWithCorrelationName();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */
    protected function _selectJoinInner()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->joinInner('zfbugs_products', "$products.$product_id = $bugs_products.$product_id");
        return $select;
    }

    public function testSelectJoinInner()
    {
        $select = $this->_selectJoinInner();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */
    protected function _selectJoinLeft()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs')
            ->joinLeft('zfbugs_products', "$bugs.$bug_id = $bugs_products.$bug_id");
        return $select;
    }

    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(9, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }

    /**
     * Returns a select object that uses table aliases and specifies a mixed ordering of columns,
     * for testing whether the user-specified ordering is preserved.
     *
     * @return Zend_Db_Select
     */
    protected function _selectJoinLeftTableAliasesColumnOrderPreserve()
    {
        $bugsBugId        = $this->_db->quoteIdentifier('b.bug_id');
        $bugsProductBugId = $this->_db->quoteIdentifier('bp.bug_id');

        $select = $this->_db->select()
            ->from(array('b' => 'zfbugs'), array('b.bug_id', 'bp.product_id', 'b.bug_description'))
            ->joinLeft(array('bp' => 'zfbugs_products'), "$bugsBugId = $bugsProductBugId", array());

        return $select;
    }

    /**
     * Ensures that when table aliases are used with a mixed ordering of columns, the user-specified
     * column ordering is preserved.
     *
     * @return void
     */
    public function testJoinLeftTableAliasesColumnOrderPreserve()
    {
        $select = $this->_selectJoinLeftTableAliasesColumnOrderPreserve();
        $this->assertRegExp('/^.*b.*bug_id.*,.*bp.*product_id.*,.*b.*bug_description.*$/s', $select->__toString());
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */
    protected function _selectJoinRight()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products')
            ->joinRight('zfbugs', "$bugs_products.$bug_id = $bugs.$bug_id");
        return $select;
    }

    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(9, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }

    /**
     * Test adding a cross join to a Zend_Db_Select object.
     */
    protected function _selectJoinCross()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->joinCross('zfbugs_products');
        return $select;
    }

    public function testSelectJoinCross()
    {
        $select = $this->_selectJoinCross();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(18, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test support for schema-qualified table names in join(),
     * e.g. join('schema.table', 'condition')
     */
    protected function _selectJoinQualified()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $schema = $this->_util->getSchema();
        $select = $this->_db->select()
            ->from('zfproducts')
            ->join("$schema.zfbugs_products", "$products.$product_id = $bugs_products.$product_id");
        return $select;
    }

    public function testSelectJoinQualified()
    {
        $select = $this->_selectJoinQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding a WHERE clause to a Zend_Db_Select object.
     */
    protected function _selectWhere()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 2");
        return $select;
    }

    public function testSelectWhere()
    {
        $select = $this->_selectWhere();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * test adding more WHERE conditions,
     * which should be combined with AND by default.
     */
    protected function _selectWhereAnd()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 2")
            ->where("$product_id = 1");
        return $select;
    }

    public function testSelectWhereAnd()
    {
        $select = $this->_selectWhereAnd();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test support for where() with a parameter,
     * e.g. where('id = ?', 1).
     */
    protected function _selectWhereWithParameter()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = ?", 2);
        return $select;
    }

    public function testSelectWhereWithParameter()
    {
        $select = $this->_selectWhereWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test adding an OR WHERE clause to a Zend_Db_Select object.
     */
    protected function _selectWhereOr()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->orWhere("$product_id = 1")
            ->orWhere("$product_id = 2");
        return $select;
    }

    public function testSelectWhereOr()
    {
        $select = $this->_selectWhereOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals(2, $result[1]['product_id']);
    }

    /**
     * Test support for where() with a parameter,
     * e.g. orWhere('id = ?', 2).
     */
    protected function _selectWhereOrWithParameter()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->orWhere("$product_id = ?", 1)
            ->orWhere("$product_id = ?", 2);
        return $select;
    }

    public function testSelectWhereOrWithParameter()
    {
        $select = $this->_selectWhereOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals(2, $result[1]['product_id']);
    }

    /**
     * Test adding a GROUP BY clause to a Zend_Db_Select object.
     */
    protected function _selectGroupBy()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group('bug_id')
            ->order('bug_id');
        return $select;
    }

    public function testSelectGroupBy()
    {
        $select = $this->_selectGroupBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test support for qualified table in group(),
     * e.g. group('schema.table').
     */
    protected function _selectGroupByQualified()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group("zfbugs_products.bug_id")
            ->order('bug_id');
        return $select;
    }

    public function testSelectGroupByQualified()
    {
        $select = $this->_selectGroupByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test support for Zend_Db_Expr in group(),
     * e.g. group(new Zend_Db_Expr('id+1'))
     */
    protected function _selectGroupByExpr()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id'=>new Zend_Db_Expr("$bug_id+1"), new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group(new Zend_Db_Expr("$bug_id+1"))
            ->order(new Zend_Db_Expr("$bug_id+1"));
        return $select;
    }

    public function testSelectGroupByExpr()
    {
        $select = $this->_selectGroupByExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(2, $result[0]['bug_id'],
            'Expected first bug_id to be 2');
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first group to be 2');
        $this->assertEquals(3, $result[1]['bug_id'],
            'Expected second bug_id to be 3');
        $this->assertEquals(1, $result[1]['thecount'],
            'Expected count(*) of second group to be 1');
    }

    /**
     * Test support for automatic conversion of a SQL
     * function to a Zend_Db_Expr in group(),
     * e.g.  group('LOWER(title)') should give the same
     * result as group(new Zend_Db_Expr('LOWER(title)')).
     */

    protected function _selectGroupByAutoExpr()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id'=>"ABS($bugs_products.$bug_id)", new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group("ABS($bugs_products.$bug_id)")
            ->order("ABS($bugs_products.$bug_id)");
        return $select;
    }

    public function testSelectGroupByAutoExpr()
    {
        $select = $this->_selectGroupByAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount'], 'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */
    protected function _selectHaving()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->having('COUNT(*) > 1')
            ->order('bug_id');
        return $select;
    }

    public function testSelectHaving()
    {
        $select = $this->_selectHaving();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
    }

    protected function _selectHavingAnd()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->having('COUNT(*) > 1')
            ->having('COUNT(*) = 1')
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingAnd()
    {
        $select = $this->_selectHavingAnd();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test support for parameter in having(),
     * e.g. having('count(*) > ?', 1).
     */

    protected function _selectHavingWithParameter()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->having('COUNT(*) > ?', 1)
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingWithParameter()
    {
        $select = $this->_selectHavingWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */

    protected function _selectHavingOr()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->orHaving('COUNT(*) > 1')
            ->orHaving('COUNT(*) = 1')
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingOr()
    {
        $select = $this->_selectHavingOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test support for parameter in orHaving(),
     * e.g. orHaving('count(*) > ?', 1).
     */
    protected function _selectHavingOrWithParameter()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->orHaving('COUNT(*) > ?', 1)
            ->orHaving('COUNT(*) = ?', 1)
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingOrWithParameter()
    {
        $select = $this->_selectHavingOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test adding an ORDER BY clause to a Zend_Db_Select object.
     */
    protected function _selectOrderBy()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id');
        return $select;
    }

    public function testSelectOrderBy()
    {
        $select = $this->_selectOrderBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectOrderByArray()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order(array('product_name', 'product_id'));
        return $select;
    }

    public function testSelectOrderByArray()
    {
        $select = $this->_selectOrderByArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 3');
        $this->assertEquals('Linux', $result[0]['product_name']);
        $this->assertEquals(2, $result[0]['product_id']);
    }

    protected function _selectOrderByAsc()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("product_id ASC");
        return $select;
    }

    public function testSelectOrderByAsc()
    {
        $select = $this->_selectOrderByAsc();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectOrderByDesc()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("product_id DESC");
        return $select;
    }

    public function testSelectOrderByDesc()
    {
        $select = $this->_selectOrderByDesc();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(3, $result[0]['product_id']);
    }

    /**
     * Test support for qualified table in order(),
     * e.g. order('schema.table').
     */
    protected function _selectOrderByQualified()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("zfproducts.product_id");
        return $select;
    }

    public function testSelectOrderByQualified()
    {
        $select = $this->_selectOrderByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    /**
     * Test support for Zend_Db_Expr in order(),
     * e.g. order(new Zend_Db_Expr('id+1')).
     */
    protected function _selectOrderByExpr()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order(new Zend_Db_Expr("1"));
        return $select;
    }

    public function testSelectOrderByExpr()
    {
        $select = $this->_selectOrderByExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    /**
     * Test automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. order('LOWER(title)')
     * should give the same result as
     * order(new Zend_Db_Expr('LOWER(title)')).
     */
    protected function _selectOrderByAutoExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("ABS($products.$product_id)");
        return $select;
    }

    public function testSelectOrderByAutoExpr()
    {
        $select = $this->_selectOrderByAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    /**
     * Test adding a LIMIT clause to a Zend_Db_Select object.
     */
    protected function _selectLimit()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limit(1);
        return $select;
    }

    public function testSelectLimit()
    {
        $select = $this->_selectLimit();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectLimitNone()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limit(); // no limit
        return $select;
    }

    public function testSelectLimitNone()
    {
        $select = $this->_selectLimitNone();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
    }

    protected function _selectLimitOffset()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limit(1, 1);
        return $select;
    }

    public function testSelectLimitOffset()
    {
        $select = $this->_selectLimitOffset();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test the limitPage() method of a Zend_Db_Select object.
     */
    protected function _selectLimitPageOne()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limitPage(1, 1); // first page, length 1
        return $select;
    }

    public function testSelectLimitPageOne()
    {
        $select = $this->_selectLimitPageOne();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectLimitPageTwo()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limitPage(2, 1); // second page, length 1
        return $select;
    }

    public function testSelectLimitPageTwo()
    {
        $select = $this->_selectLimitPageTwo();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test the getPart() and reset() methods of a Zend_Db_Select object.
     */
    public function testSelectGetPartAndReset()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->limit(1);
        $count = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
        $this->assertEquals(1, $count);

        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $count = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
        $this->assertNull($count);

        $select->reset(); // reset the whole object
        $from = $select->getPart(Zend_Db_Select::FROM);
        $this->assertTrue(empty($from));
    }

}
