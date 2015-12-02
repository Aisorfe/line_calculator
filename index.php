<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа 2. Калькулятор.</title>
</head>
<body>

    <form method="post">
        <label for="calc">Введите выражение:</label><br><br>
        <input type="text" name="expression"></input><br><br>
        <input type="submit" value="Посчитать"><br><br>
    </form>

<?php

$acceptable_chars = array(" ", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "(", ")", "+", "-", "*", "/");

function calculate($expression) {
    if ( $expression != '' ) {
        if ( !is_correct_expression($expression) ) {
            echo 'Выражение содержит недопустимые символы.';
            return;
        }
    }
    $outstring = get_polish_notation($expression);
    $result = calculate_polish_notation($outstring);
    echo $result;
}

function is_correct_expression($expression) {
    for ($i = 0; $i < strlen($expression); $i++) {
        if ( !in_array($expression[$i], $GLOBALS['acceptable_chars']) ) return false;
    }
    return true;
}

function spot_priority($operation) {
    switch($operation) {
        case '*':
        case '/':
             return 3;
        case '-':
        case '+':
             return 2;
        case '(':
             return 1;
    }
}

function get_polish_notation($expression) {
    $current_op_stack = new Stack();
    $outstring = '';
    for ($i = 0; $i < strlen($expression); $i++) {
        if ( $expression[$i] == ')' ) {
            while ( $current_op_stack->top() != '(' ) {
                $outstring .= $current_op_stack->pop();
            }
            $current_op_stack->pop();
        }
        if ( $expression[$i] == '(' ) {
            $current_op_stack->push($expression[$i]);
        }
        if ( ($expression[$i] == '-') or ($expression[$i] == '+') or ($expression[$i] == '*') or ($expression[$i] == '/') ) {
            if ( $current_op_stack->is_empty() ) {
                $current_op_stack->push($expression[$i]);
            } elseif ( spot_priority($expression[$i]) > spot_priority($current_op_stack->top()) ) {
                $current_op_stack->push($expression[$i]);
            } else {
                while ( (!$current_op_stack->is_empty()) and (spot_priority($current_op_stack->top()) >= spot_priority($expression[$i])) ) {
                    $outstring .= $current_op_stack->pop();
                }
                $current_op_stack->push($expression[$i]);
            }
        }
        if ( is_numeric($expression[$i]) ) {
            $outstring .= '.';
            do {
                $outstring .= $expression[$i];
            } while ( is_numeric($expression[++$i]) );
            --$i;
        }
    }
    while ( !$current_op_stack->is_empty() ) {
        $outstring .= $current_op_stack->pop();
    }
    return $outstring;
}

function calculate_polish_notation($notation) {
    $calc_stack = new Stack();
    for ($i = 0; $i < strlen($notation); $i++) {
        $current_number = '';
        if ( $notation[$i] == '.' ) {
            ++$i;
            do {
                $current_number .= $notation[$i];
            } while ( is_numeric($notation[++$i]) );
            --$i;
            $calc_stack->push($current_number);
            $current_number = '';
        }
        if ( ($notation[$i] == '-') or ($notation[$i] == '+') or ($notation[$i] == '*') or ($notation[$i] == '/') ) {
            $a = $calc_stack->pop();
            $b = $calc_stack->pop();
            if ($notation[$i] == '-') $result = $a - $b;
            elseif ($notation[$i] == '+') $result = $a + $b;
            elseif ($notation[$i] == '*') $result = $a * $b;
            elseif ($notation[$i] == '/') $result = $a / $b;
            $calc_stack->push($result);
        }
    }
    return $result;
}

class Stack {
    protected $stack;
    public function __construct() {
        $this->stack = array();
    }
    public function push($item) {
        array_unshift($this->stack, $item);
    }
    public function pop() {
        return array_shift($this->stack);
    }
    public function top() {
        return current($this->stack);
    }
    public function is_empty() {
        return empty($this->stack);
    }
    public function show_all() {
        for ($i = 0; $i < count($this->stack); $i++) {
            echo $this->stack[$i].' ';
        }
    }
}

$current_expression = $_POST['expression'];
calculate($current_expression);

?>

</body>
</html>