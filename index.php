<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Строковый калькулятор.</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <div class="container">
        <br><br><br><br>
        <div class="col-md-6 col-md-offset-3">
            <form method="post">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" placeholder="Введите выражение..." name="expression">
                    <span class="input-group-btn">
                        <button class="btn btn-success" type="submit">Вычислить!</button>
                    </span>
                </div>
            </form>
        </div>
    </div>

<?php

$acceptable_chars = array(" ", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "(", ")", "+", "-", "*", "/");

function calculate($expression) {
        if ( !is_correct_expression($expression) ) {
            show_error('Выражение содержит недопустимые символы.');
            return;
        }
    $outstring = get_polish_notation($expression);
    $result = calculate_polish_notation($outstring);
    if ( is_numeric($result) ) {
    echo '<div class="col-md-6 col-md-offset-3">
              <br><br>
              <div class="alert alert-success" role="alert">Результат: '.$result.'</div>
          <div>';
    }
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
            if ($notation[$i] == '-') $result = $b - $a;
            elseif ($notation[$i] == '+') $result = $a + $b;
            elseif ($notation[$i] == '*') $result = $a * $b;
            elseif ($notation[$i] == '/') {
                if ( $a == 0 ) {
                    show_error('В выражение возникло деление на ноль.');
                    return;
                } else $result = $b / $a;
            }
            $calc_stack->push($result);
        }
    }
    return $result;
}

function show_error($message) {
    echo '<div class="col-md-6 col-md-offset-3">
              <br><br>
              <div class="alert alert-danger" role="alert">'.$message.'</div>
          <div>';
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