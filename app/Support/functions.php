<?php

namespace App\Support;

class Functions {

    // Загрузчик фото (Возвращает ссылку на фотографию)
    function loadFoto($link)
    {
        $folder = $_SERVER['DOCUMENT_ROOT'].'/images/'.$link;    // Папка загрузки
        if (!file_exists($folder)) {                             // Если нет папки загрузки
            mkdir($_SERVER['DOCUMENT_ROOT'].'/images/'.$link);   // Создаётся папка загрузки
        }
        $listFiles = scandir($folder);
        $path = $_FILES['file']['tmp_name'];
        $fileInfo = pathinfo($_FILES['file']['name']);
        return $this->transliteration($fileInfo['filename'], $fileInfo['extension'], $listFiles, $path, $folder);
    }

            
    // Транслитерация имени загружаемого фото
    function transliteration($name, $extension, $listFiles, $path, $folder)
    {
        $newName = '';
        $arr = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'j','з'=>'z','и'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'i','ь'=>'','э'=>'e','ю'=>'u','я'=>'ya', 'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'YO','Ж'=>'J','З'=>'Z','И'=>'I','Й'=>'I','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'CH','Ш'=>'SH','Щ'=>'SCH','Ъ'=>'','Ы'=>'I','Ь'=>'','Э'=>'E','Ю'=>'U','Я'=>'YA', 'a' => 'a', 'b' => 'b', 'c' => 'c', 'd' => 'd', 'e' => 'e', 'f' => 'f', 'g' => 'g', 'h' => 'h', 'i' => 'i', 'j' => 'j', 'k' => 'k', 'l' => 'l', 'm' => 'm', 'n' => 'n', 'o' => 'o', 'p' => 'p', 'q' => 'q', 'r' => 'r', 's' => 's', 't' => 't', 'u' => 'u', 'v' => 'v', 'w' => 'w', 'x' => 'x', 'y' => 'y', 'z' => 'z', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', 'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O', 'P' => 'P', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W', 'X' => 'X', 'Y' => 'Y', 'Z' => 'Z', '-' => '-', '_' => '_', '0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9'];
        $arStr = preg_split('//u', $name);
    
        foreach($arStr as $letter) {
            if ($letter == '') continue;
            if (!isset($newName)) $newName = $arr[$letter];
            else if (isset($newName)) $newName .= $arr[$letter];
        }
                
        $findFiles = preg_grep("/^".$newName."(.+)?\.".$extension."$/", $listFiles);
                    
        $filename = $newName.(count( $findFiles) > 0 ? '_' .(count( $findFiles) + 1) : '').'.'.$extension;
        move_uploaded_file($path, $folder . '/' . $filename);
    
        return $filename;
    }


    // Обработка данных
    function loadProduct($array)
    {
        $author_id = $array['userData']['author_id'];
        $code = mb_strtolower(str_replace(' ', '_', $array['name']));   // Код товара (str_replace заменяет символы, mb_strtolower приводит к нижнему регистру)
        $image = $this->loadFoto('foto_products');
        
        $data = array( 
            'category_code' => $array['category_code'],
            'author_id' => $author_id,
            'name' => $array['name'],
            'code' => $code,
            'description' => $array['description'],
            'image' => $image,
            'price' => $array['price']
        );
        return $data;
    }
    

    // Доступ к удалению комментария
    function resolutionDelete($focusUser, $cookieUser)
    {
        if (isset($cookieUser['position']) && (
            ($cookieUser['position'] == 'administrator' && ($focusUser['position'] == 'moderator' || $focusUser['position'] == 'operator' || $focusUser['position'] == 'user' || $focusUser['position'] == 'banned')) || 
            ($cookieUser['position'] == 'moderator' && ($focusUser['position'] == 'user' || $focusUser['position'] == 'banned')) || 
            ($focusUser['id'] == $cookieUser['author_id']))
        ) return 'YES';
        else return 'NO';
    }


    // Сообщение о принятии заказа
    function messageOrder()
    {
        return '
        <style>.order_enter {display: block;}</style>
        <div class="order_enter">
            <div class="order_enter_window">
                <h2>Ваш заказ принят</h2>
                <p>Ожидайте звонка оператора</p>
            </div>
        </div>';
    }
}