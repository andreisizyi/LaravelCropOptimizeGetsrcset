<?php
 
namespace App\Http\Controllers\Images;
//Контроллер для подпапок
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Category;
use DB;

class AxioGallery extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public static function get($type)
    {
    
    $dir = 'storage/'.$type.'/';

    //Проверка директории на пустоту
    function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }
    if (!is_dir_empty($dir)) {
        $files = chdir($dir);
        array_multisort(array_map('filemtime', ($files = glob("*.*"))), SORT_DESC, $files); //Сортируем все содержимое по дате файла
        $files = array_values(preg_grep('/(_360.)|(_700.)|(_1200.)|(.webp)|(.)[ \/]|(..)[ \/]/', $files, PREG_GREP_INVERT)); //Делаем новый массив только из основных фото и обнуляем индексы 
    } else {
        $files = Null;
    }
    	$data = \App\Http\Controllers\paginateArray::do($files, 6, 'dashboard/apartments/mediaselect');
    	return response()->json($data);
    }
}