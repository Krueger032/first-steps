<?
namespace Linkor\Redirect;

class Storage{
    public static function CSVWrite($fname, $data, $sep = ";"){
        $f = fopen($fname, "w");
        if ($f === false) {
            return;
        }
        //fputcsv($f, "");
        if($data !== Array()){
            foreach ($data as $row) {
                foreach ($row as $i => $v) {
                    $row[$i] = trim($row[$i]);
                }
                ksort($row);
                if (count(array_filter($row)) == 0) {
                    continue;
                }
               // $row = array_map("utf8_decode", $row);
                fputcsv($f, $row, $sep);
            }
            fclose($f);
        }
    }
    public static function CSVRead($fname, $sep = ";"){
        $result = [];
        if(file_exists($fname)){
            $f = fopen($fname, "r");
            if ($f === false) {
                return $result;
            }
            while (($row = fgetcsv($f, 4000, $sep)) !== false) {
                $result[] = array_map("trim", $row);
            }
            fclose($f);
        }

        return $result;
    }

    public static function PHPWrite($fname, $data){
        file_put_contents(
            $fname,
            "<?php\nreturn " . var_export($data, true) . ";"
        );
    }
}
