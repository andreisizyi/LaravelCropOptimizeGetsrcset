<?php

namespace App\Http\Controllers\Images;

//Контроллер для подпапок
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Image;

//Для WebP и компрессора
use WebPConvert\WebPConvert;
use ArtisansWeb\Optimizer;

class MediaGallery extends Controller
{
	public function store(Request $request) {
		
		//Записываем созание миниатюр в функцию
		function create_thumbnail($filename, $extension, $sufix, $width, $height) {
			//Через эту функцию уже работаем с оптимизированным файлом и делаем пару размеров и webp
			//Берем файл по пути
			$thumbnailpath = public_path('storage/images/'.$filename.'.'.$extension);
			//Делаем новый путь те название файла чтобы взять уже оптимизированный
			$newpath = public_path('storage/images/'.$filename.$sufix.'.'.$extension);
			//Копируем файл в новый
			copy($thumbnailpath, $newpath);
			//Resize image here
			if ($width !== 0) {
				$img = Image::make($newpath)->resize($width, $height, function($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->limitColors(255);
				//->limitColors(255, '#ffffff');
				$img->save($newpath);
			}
			//Конвертируем в WebP
			$source = 'storage/images/'.$filename.$sufix.'.'.$extension;
			$destination = 'storage/images/'.$filename.$sufix.'.webp';
			$options = [];
			WebPConvert::convert($source, $destination, $options);
		}
	
		//Перебираем массив файлов
		$images = $request->file();
		foreach ($images as $file) {
			foreach ($file as $image) {
				//get filename with extension
				$filenamewithextension = $image->getClientOriginalName();	
				//get filename without extension
				$filename = \App\Http\Controllers\Translit::do(pathinfo($filenamewithextension, PATHINFO_FILENAME));
				//get file extension
				$extension = $image->getClientOriginalExtension();
				//Записываем миниатюру
				$filenametostore = $filename.'.'.$extension;
				//Upload File
				//$request->file('image')->storeAs('public/images/', $filenametostore);
				//Прямая загрузка в public
				//move_uploaded_file($image, 'storage/images/'.$filenametostore);
				if ($extension != 'svg') {
					//Сохраняем изображение
					Image::make($image)->limitColors(255)->save('storage/images/'.$filenametostore);
					//Image::make($image)->limitColors(255, '#ff9900')->save('storage/images/'.$filenametostore);
					//Получаем путь к изображению
					$thumbnailpath = public_path('storage/images/'.$filenametostore);
					//Оптимизируем изображение
					$img = new Optimizer();
					$img->optimize($thumbnailpath);
					//Конвертируем в WebP
					$source = 'storage/images/'.$filename.'.'.$extension;
					$destination = 'storage/images/'.$filename.'.webp';
					$options = [];
					WebPConvert::convert($source, $destination, $options);
					//Узнаем соотношение стороно изображения, для раздачи резсайзеру
					list($w, $h) = getimagesize('storage/images/'.$filenametostore);
					$AspRt = $w/$h;
					if ($AspRt < 1) : $AspRt = $AspRt + 1; endif;
					//Ресайзим
					create_thumbnail($filename, $extension, '_360', '360', round(360*$AspRt));
					create_thumbnail($filename, $extension, '_700', '700', round(700*$AspRt));
					create_thumbnail($filename, $extension, '_1200', '1200', round(1200*$AspRt));
				} else {
					move_uploaded_file($image, 'storage/images/'.$filenametostore);
				}
			}	
		}
			
		return redirect()->back();
		
	}
	
	public static function del($filename) {
		$info = pathinfo($filename);
		$filename = basename($filename,'.'.$info['extension']);
		//Удаляемм стандартные форматы
		$name_no_format = 'storage/images/'.$filename;
		if (file_exists($name_no_format.'.'.$info['extension'])) {
			unlink($name_no_format.'.'.$info['extension']);
		}
		if (file_exists($name_no_format.'_360.'.$info['extension'])) {
			unlink($name_no_format.'_360.'.$info['extension']);
		}
		if (file_exists($name_no_format.'_700.'.$info['extension'])) {
			unlink($name_no_format.'_700.'.$info['extension']);
		}
		if (file_exists($name_no_format.'_1200.'.$info['extension'])) {
			unlink($name_no_format.'_1200.'.$info['extension']);
		}
		//Удаляемм оптимизированный WebP
		if (file_exists($name_no_format.'.webp')) {
			unlink($name_no_format.'.webp');
		}
		if (file_exists($name_no_format.'_360.webp')) {
			unlink($name_no_format.'_360.webp');
		}
		if (file_exists($name_no_format.'_700.webp')) {
			unlink($name_no_format.'_700.webp');
		}
		if (file_exists($name_no_format.'_1200.webp')) {
			unlink($name_no_format.'_1200.webp');
		}
		return redirect()->back();
	}
	
}
