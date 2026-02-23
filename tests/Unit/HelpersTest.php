<?php

namespace Tpojka\Confer\Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * Test confer_make_list with one item.
     *
     * @return void
     */
    public function test_confer_make_list_with_one_item()
    {
        $items = ['John'];
        $result = confer_make_list($items);
        $this->assertEquals('John', $result);
    }

    /**
     * Test confer_make_list with two items.
     *
     * @return void
     */
    public function test_confer_make_list_with_two_items()
    {
        $items = ['John', 'Jane'];
        $result = confer_make_list($items);
        $this->assertEquals('John and Jane', $result);
    }

    /**
     * Test confer_make_list with three items.
     *
     * @return void
     */
    public function test_confer_make_list_with_three_items()
    {
        $items = ['John', 'Jane', 'Doe'];
        $result = confer_make_list($items);
        $this->assertEquals('John, Jane and Doe', $result);
    }

    /**
     * Test confer_make_list with Oxford comma.
     *
     * @return void
     */
    public function test_confer_make_list_with_oxford_comma()
    {
        $items = ['John', 'Jane', 'Doe'];
        $result = confer_make_list($items, true);
        $this->assertEquals('John, Jane, and Doe', $result);
    }
}
