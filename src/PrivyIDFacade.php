<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-15
 * Time: 20:26
 */

namespace BlackIT\PrivyID;
use Illuminate\Support\Facades\Facade;

class PrivyIDFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'blackit-privyid';
    }
}
