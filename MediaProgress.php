<?php

namespace App\Http\Controllers\Images;

//Контроллер для подпапок
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Image;

//Для WebP и компрессора
use WebPConvert\WebPConvert;
use ArtisansWeb\Optimizer;

class MediaProgress extends Controller
{
	public function store(Request $request) {
		
		//Записываем созание миниатюр в функцию
		function create_thumbnail($filename, $extension, $sufix, $width, $height) {
			//Через эту функцию уже работаем с оптимизированным файлом и делаем пару размеров и webp
			//Берем файл по пути
			$thumbnailpath = public_path('storage/progress/'.$filename.'.'.$extension);
			//Делаем новый путь те название файла чтобы взять уже оптимизированный
			$newpath = public_path('storage/progress/'.$filename.$sufix.'.'.$extension);
			//Копируем файл в новый
			copy($thumbnailpath, $newpath);
			//Resize image here
			if ($width !== 0) {
				//Жесткая обрезка под нужный размер, но не больше размера изначального изображения
				$img = Image::make($newpath)->fit($width, $height, function($constraint) {
					$constraint->upsize();
				})->limitColors(255)->save($newpath);
			}
			//Конвертируем в WebP
			$source = 'storage/progress/'.$filename.$sufix.'.'.$extension;
			$destination = 'storage/progress/'.$filename.$sufix.'.webp';
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
				//$filename = $filename.'-----'.uniqid();
				$filename = $filename.'_'.date("m-d-y_H-i-s");
				$filenametostore = $filename.'.'.$extension;
				//Upload File
				//$request->file('image')->storeAs('public/images/', $filenametostore);
						//Прямая загрузка в public
						//move_uploaded_file($image, 'storage/progress/'.$filenametostore);

				if ($extension != 'svg') {
					//Обрезаем изображение до нужного формата от центра и сохраняем
					//Узнаем стороны изображения
					list($w, $h) = getimagesize($image);
					$h = $w / ( 1280 / 720 );
					$AspRt = $w / $h;
					if ($AspRt < 1) : $AspRt = $AspRt + 1; endif;
					//Обрезаем согласно новому отношению сторон с небольшим разрешением чем было
					Image::make($image)->fit(1330, round(1330/$AspRt), function($constraint) {
						$constraint->upsize();
					})->limitColors(255)->save('storage/progress/'.$filenametostore);
					//Получаем путь к изображению
					$thumbnailpath = public_path('storage/progress/'.$filenametostore);
					//Оптимизируем изображение
					$img = new Optimizer();
					$img->optimize($thumbnailpath);
					//Конвертируем в WebP
					$source = 'storage/progress/'.$filename.'.'.$extension;
					$destination = 'storage/progress/'.$filename.'.webp';
					$options = [];
					WebPConvert::convert($source, $destination, $options);
					//Ресайзим
					create_thumbnail($filename, $extension, '_360', '360', round(360/$AspRt) );
					create_thumbnail($filename, $extension, '_700', '700', round(700/$AspRt) );
					create_thumbnail($filename, $extension, '_1200', '1200', round(1200/$AspRt) );
				} else {
					move_uploaded_file($image, 'storage/progress/'.$filenametostore);
				}
			}	
		}
			
		return redirect()->back();
		
	}
	
	public static function del($filename) {
		$info = pathinfo($filename);
		$filename = basename($filename,'.'.$info['extension']);
		//Удаляемм стандартные форматы
		$name_no_format = 'storage/progress/'.$filename;
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
