<?php

class TestBase {

    protected $fails = 0;
    protected $tests = 0;

    protected $html = '';

    protected function _beforeAll() {
        UsuarioLogin::login('lea', 'e2f9f9c223d5572a608a92557e8271aa250eef1d');
    }
    protected function _afterAll() {
        Transaction::rollback();

        $this->_printHtml();
    }
    protected function _beforeEach() {
        Transaction::begin();
    }
    protected function _afterEach() {
        Transaction::rollback();
        //Transaction::commit();
    }

    public function run() {
        if (!Config::desarrollo()) {
            $this->_error('No se pueden correr tests en un entorno productivo');
            return;
        }

        $this->_beforeAll();

        foreach ($this->_getTestsList() as $testMethodName) {
            try {
                $this->_beforeEach();

                $this->_sepparator(str_replace('_', ' ', Funciones::snakeCase($testMethodName)));
                $this->$testMethodName();

                $this->_afterEach();
            } catch (Exception $ex) {
                $this->_error($ex->getMessage());
                try {
                    $this->_afterEach();
                } catch (Exception $ex2) {
                    $this->_error($ex2->getMessage());
                }
            }
        }

        $this->_afterAll();
    }

    protected function _getTestsList() {
        $list = array();
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (substr($method, 0, 1) !== '_' && $method !== 'run') {
                $list[] = $method;
            }
        }

        return $list;
    }

    protected function _sepparator($description) {
        $this->html .= '<h2>' . $description . '</h2>';
    }

    protected function _assert($bool, $description = '', $extraDataIfFail = '[]') {
        if (!$description) {
            $description = 'Assertion failed';
        }
        $this->html .= '<p class="' . ($bool ? 'green' : 'red') . '"><span>[' . ($bool ? 'SUCCESS' : 'FAIL') . ']</span>: ' . $description . ($bool ? '' : (' | ' . $extraDataIfFail)) . '</p>';

        $this->tests++;
        !$bool && $this->fails++;

        return $bool;
    }

    protected function _error($message) {
        $this->html .= '<h2 class="red"><span>[ERROR]</span>: ' . $message . '</h2>';
    }

    protected function _printHtml() {
        echo '
            <html>
            <head>
                <style>
                    span{font-weight:bold;}
                    .green{color:green;}
                    .red{color:red;}
                </style>
            </head>
            <body>
                <h2>Summary</h2>
                <ul>
                    <li class="' . ($this->fails ? 'red' : '') . '">Failed: ' . $this->fails . '</li>
                    <li class="' . ($this->fails ? '' : 'green') . '">Succeded: ' . ($this->tests - $this->fails) . '</li>
                </ul>
                ' . $this->html . '
            </body>
            </html>
        ';
    }
}
