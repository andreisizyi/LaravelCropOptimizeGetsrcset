<?php

namespace App\Http\Controllers\Images;

//Контроллер для подпапок
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public static function get($path, $title, $size_m, $size, $type) {
        $info = pathinfo($path);
        if (($path) && (\File::exists(public_path().'/storage/'.$type.'/'.$path))) {
            $img_params = getimagesize(public_path().'/storage/'.$type.'/'.$path);
            $name = basename($path,'.'.$info['extension']);
            $ext = $info['extension'];
            $width = $img_params[0];
            $height = $img_params[1];
            $height_rel = ($height*100)/$width.'%';
            echo '
                <picture style="display: block; position: relative; padding-bottom:'.$height_rel.'" v-view.once="onImage">
                    <source type="image/webp" attrsrcset="/storage/'.$type.'/'.$name.'_360.webp 360w, /storage/'.$type.'/'.$name.'_700.webp 700w, /storage/'.$type.'/'.$name.'_1200.webp 1200w, /storage/'.$type.'/'.$name.'.webp" title="'.$title.'" alt="'.$title.'" sizes="(max-width: 500px) '.$size_m.'vw, '.$size.'vw">
                    <img src="/storage/site/plug.png" style="position: absolute; top: 0; left: 0; width: 100%; height: auto" width="'.$width.'" height="'.$height.'" attrsrc="/storage/'.$type.'/'.$name.'.'.$ext.'" attrsrcset="/storage/'.$type.'/'.$name.'_360.jpg 360w, /storage/'.$type.'/'.$name.'_700.'.$ext.' 700w, /storage/'.$type.'/'.$name.'_1200.jpg 1200w, /storage/'.$type.'/'.$name.'.'.$ext.'" title="'.$title.'" alt="'.$title.'" sizes="(max-width: 500px) '.$size_m.'vw, '.$size.'vw">	
                </picture>
            ';
        }
    }

    public static function get_700($path, $title, $size_m, $size, $type) {
        $info = pathinfo($path);
        if (($path) && (\File::exists(public_path().'/storage/'.$type.'/'.$path))) {
            $name = basename($path,'.'.$info['extension']);
            $ext = $info['extension'];
            $img_params = getimagesize(public_path().'/storage/'.$type.'/'.$path);
            $width = $img_params[0];
            $height = $img_params[1];
            $height_rel = ($height*100)/$width.'%';
            $height700 = 700/($width/$height);
            echo '
                <picture style="display: block; position: relative; padding-bottom:'.$height_rel.'" v-view.once="onImage">
                    <source type="image/webp" attrsrcset="/storage/'.$type.'/'.$name.'_360.webp 360w, /storage/'.$type.'/'.$name.'_700.webp" title="'.$title.'" alt="'.$title.'" sizes="(max-width: 500px) '.$size_m.'vw, '.$size.'vw">
                    <img src="/storage/site/plug.png" style="position: absolute; top: 0; left: 0; width: 100%; height: auto" width="700" height="'.$height700.'" attrsrc="/storage/'.$type.'/'.$name.'.'.$ext.'" attrsrcset="/storage/'.$type.'/'.$name.'_360.jpg 360w, /storage/'.$type.'/'.$name.'_700.'.$ext.'" title="'.$title.'" alt="'.$title.'" sizes="(max-width: 500px) '.$size_m.'vw, '.$size.'vw">	
                </picture>
            ';
        }
    }

    public static function get_axio_700($path, $title, $size_m, $size, $type) {
        $info = pathinfo($path);
        if (($path) && (\File::exists(public_path().'/storage/'.$type.'/'.$path))) {
            $name = basename($path,'.'.$info['extension']);
            $ext = $info['extension'];
            $img_params = getimagesize(public_path().'/storage/'.$type.'/'.$path);
            $width = $img_params[0];
            $height = $img_params[1];
            $height_rel = ($height*100)/$width.'%';
            $height700 = 700/($width/$height);
            return '
                <picture style="display: block; position: relative; padding-bottom:'.$height_rel.'">
                    <source type="image/webp" attrsrcset="/storage/'.$type.'/'.$name.'_360.webp 360w, /storage/'.$type.'/'.$name.'_700.webp" title="'.$title.'" alt="'.$title.'">
                    <img src="/storage/site/plug.png" style="position: absolute; top: 0; left: 0; width: 100%; height: auto" width="700" height="'.$height700.'" attrsrc="/storage/'.$type.'/'.$name.'.'.$ext.'" attrsrcset="/storage/'.$type.'/'.$name.'_360.jpg 360w, /storage/'.$type.'/'.$name.'_700.'.$ext.'" title="'.$title.'" alt="'.$title.'" sizes="(max-width: 500px) '.$size_m.'vw, '.$size.'vw">	
                </picture>
            ';
        }
    }
}
