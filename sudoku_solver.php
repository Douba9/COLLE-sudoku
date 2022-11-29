<?php

class Colle_Sudoku {

    private $next_arr = array();
    private $grids = array();
    private $cols_begin = array();
    private $trackTime = array();

    public function __construct() {
        $this->trackTime['start'] = microtime(true);
    }

    private function grids() {
        $grids = array();
        foreach ($this->next_arr as $i => $row) {
            if ($i <= 2) {
                $row_number = 1;
            }
            if ($i > 2 && $i <= 5) {
                $row_number = 2;
            }
            if ($i > 5 && $i <= 8) {
                $row_number = 3;
            }

            foreach ($row as $ii => $r) {
                if ($ii <= 2) {
                    $col_number = 1;
                }
                if ($ii > 2 && $ii <= 5) {
                    $col_number = 2;
                }
                if ($ii > 5 && $ii <= 8) {
                    $col_number = 3;
                }
                $grids[$row_number][$col_number][] = $r;
            }
        }
        $this->grids = $grids;
    }

    private function columns() {
        $cols_begin = array();
        $i = 1;
        foreach ($this->next_arr as $y => $row) {
            $e = 1;
            foreach ($row as $yy => $r) {
                $cols_begin[$e][$i] = $r;
                $e++;
            }
            $i++;
        }
        $this->cols_begin = $cols_begin;
    }

    private function possibilities($i, $ii) {
        $values = array();
        if ($i <= 2) {
            $row_number = 1;
        }
        if ($i > 2 && $i <= 5) {
            $row_number = 2;
        }
        if ($i > 5 && $i <= 8) {
            $row_number = 3;
        }

        if ($ii <= 2) {
            $col_number = 1;
        }
        if ($ii > 2 && $ii <= 5) {
            $col_number = 2;
        }
        if ($ii > 5 && $ii <= 8) {
            $col_number = 3;
        }

        for ($n = 1; $n <= 9; $n++) {
            if (!in_array($n, $this->next_arr[$i]) && !in_array($n, $this->cols_begin[$ii + 1]) && !in_array($n, $this->grids[$row_number][$col_number])) {
                $values[] = $n;
            }
        }
        shuffle($values);
        return $values;
    }

    public function solve($arr) {
        while (true) {
            $this->next_arr = $arr;

            $this->columns();
            $this->grids();

            $ops = array();
            foreach ($arr as $i => $row) {
                foreach ($row as $ii => $r) {
                    if ($r == 0) {
                        $pos_vals = $this->possibilities($i, $ii);
                        $ops[] = array(
                            'rowIndex' => $i,
                            'columnIndex' => $ii,
                            'permissible' => $pos_vals
                        );
                    }
                }
            }

            if (empty($ops)) {
                return $arr;
            }

            usort($ops, array($this, 'Ops'));

            if (count($ops[0]['permissible']) == 1) {
                $arr[$ops[0]['rowIndex']][$ops[0]['columnIndex']] = current($ops[0]['permissible']);
                continue;
            }

            foreach ($ops[0]['permissible'] as $value) {
                $tmp = $arr;
                $tmp[$ops[0]['rowIndex']][$ops[0]['columnIndex']] = $value;
                if ($result = $this->solve($tmp)) {
                    return $this->solve($tmp);
                }
            }

            return false;
        }
    }

    private function Ops($a, $b) {
        $a = count($a['permissible']);
        $b = count($b['permissible']);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    public function Result() {
        echo "\n";
        foreach ($this->next_arr as $i => $row) {
            foreach ($row as $ii => $r) {
                echo $r . ' ';
            }
            echo "\n";
        }
    }

    public function __destruct() {
        $this->trackTime['end'] = microtime(true);
        $time = $this->trackTime['end'] - $this->trackTime['start'];
        echo "\nTemps d'execution : " . number_format($time, 3) . " sec\n\n";
    }

}

$arr3x3 = array(
    array(0, 6, 0, 0, 0, 0, 0, 3, 0),
    array(0, 0, 0, 5, 0, 0, 0, 0, 4),
    array(4, 1, 0, 0, 0, 9, 0, 2, 0),
    array(0, 0, 4, 0, 0, 0, 0, 0, 0),
    array(0, 3, 6, 0, 0, 0, 4, 8, 9),
    array(0, 0, 2, 4, 8, 6, 0, 0, 0),
    array(0, 2, 7, 0, 0, 0, 0, 9, 0),
    array(6, 0, 1, 9, 0, 0, 3, 0, 7),
    array(9, 4, 0, 0, 0, 5, 0, 1, 0),
);
$arr2x2 = array(
    array(0, 6, 0, 0),
    array(0, 0, 0, 5),
    array(4, 1, 0, 0),
    array(0, 0, 4, 0),
);

$sudoku = new Colle_Sudoku();

if(!isset($argv[1])){
    echo "Il manque un argument (taille du sudoku)\n";
    die();
}
if($argv[1] == "3"){
    $sudoku->solve($arr3x3);
    $sudoku->Result();
}
else if($argv[1] == "2"){
    $sudoku->solve($arr2x2);
    $sudoku->Result();
}
else {
    echo "Taille incorrect\n";
}