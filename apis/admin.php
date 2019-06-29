<?php
    if (!isset($nodirect)) die('nope');
    //die(var_dump($_REQUEST, true));
    if (isset($_GET['act'])) {
        switch (strtolower($_GET['act'])) {
//nggo login
        case 'login':
            if (isset($_POST['user'], $_POST['pass'])) {
                //filter_var($_POST['user'], FILTER)
                $user = $_POST['user']; $pass = $_POST['pass'];
                if ((validLen($user, 3, 200)) && (validLen($pass, 5, 200))) {
                    if ((preg_match('/[^@\-._A-Za-z0-9]+/', $user, $matches) === 0) ) {
                        $db['query'] = $db['con'] -> prepare('SELECT id, email, pic, access_kas, access_sales FROM users WHERE email = :email AND pass = sha2(:pass, 224)');
                        if ($db['query'] -> execute(array(
                            ':email' => $user,
                            ':pass' => $pass.KEY_AUTH
                        ))) {
                            if ($db['query'] -> rowCount() > 0) {
                                $db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
                                $_SESSION['POS']['id'] = $db['res'][0]['id'];
                                $_SESSION['POS']['email'] = $db['res'][0]['email'];
                                $_SESSION['POS']['pic'] = $db['res'][0]['pic'];
                                $_SESSION['POS']['access_kas'] = $db['res'][0]['access_kas'];
                                $_SESSION['POS']['access_sales'] = $db['res'][0]['access_sales'];
                                $_SESSION['POS']['loggedin'] = true;
                                logInfo('ID '.$db['res'][0]['id'].' logged in');
                                send(array(
                                    'status' => 'success'
                                ));
                            } else {
                                send(array(
                                    'status' => 'wrong'
                                ));
                            }
                        } else {
                            logError('Query Error while authenticating admin', $db['con'] -> errorInfo());
                            send(array(
                                'status' => 'error',
                                'desc' => 'Query Error'
                            ));
                        }
                    } else {
                        send(array(
                            'status' => 'error',
                            'desc' => 'Illegal Character '.$matches[0]
                        ));
                    }
                } else {
                    //http_response_code(500);
                    errorLength();
                }
            } else errorInvalid();
            break;
//nggo tambah data
        case 'add':
            if (!isset(
                $_POST['nama'], $_POST['email'],
                $_POST['pass1'],
                $_POST['keterangan'], $_POST['access']
            )) errorInvalid();
            $nama = trim($_POST['nama']);
            if (
                (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) == '')
                ) {
                errorInvalid();
            }
            $email = trim($_POST['email']);
            $pass1 = $_POST['pass1'];
            $keterangan = trim($_POST['keterangan']);
            $access = $_POST['access'];

            //if (preg_match('/[^ A-Za-z]/', $nama, $matches)
            if (!( (validLen($nama, INPUT_USER_NAMA_MIN, INPUT_USER_NAMA_MAX))
                && (validLen($email, INPUT_EMAIL_MIN, INPUT_EMAIL_MAX))
                && (validLen($pass1, INPUT_USER_PASS_MIN, INPUT_USER_PASS_MAX))
                && (validLen($keterangan, 0, INPUT_USER_KETERANGAN_MAX))
            )) errorLength();

            $access_kas = array();
            if (isset($_POST['kas'])) {
                foreach ($_POST['kas'] as $key => $array) {
                    array_push($access_kas, $key);
                }
            }
            $db['query'] = $db['con'] -> prepare('INSERT INTO users VALUES(null, :nama, :email, sha2(:pass, 224), 1, :access, :access_kas, "[]", :keterangan)');
            if (!$db['query'] -> execute([
                ':nama' => $nama,
                ':email' => $email,
                ':pass' => $pass1.KEY_AUTH,
                ':access' => $access,
                ':access_kas' => json_encode($access_kas),
                ':keterangan' => $keterangan,
            ])) errorQuery($db['con'] -> errorInfo());
            send([
                'status' => 'success'
            ]);

            break;
//nggo update data
        case 'edit':
            if (!isset(
                $_POST['nama'], $_POST['email'],
                $_POST['pass0'], $_POST['pass1'], $_POST['id'],
                $_POST['keterangan'], $_POST['access']
            )) errorInvalid();
            $nama = trim($_POST['nama']);
            if (
                (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) == '') or
                (filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) == '')
                ) {
                errorInvalid();
            }
            $email = trim($_POST['email']);
            $id = $_POST['id'];
            $pass0 = $_POST['pass0'];
            $pass1 = $_POST['pass1'];
            $keterangan = trim($_POST['keterangan']);
            $access = $_POST['access'];

            //if (preg_match('/[^ A-Za-z]/', $nama, $matches)
            if (!( (validLen($nama, INPUT_USER_NAMA_MIN, INPUT_USER_NAMA_MAX))
                && (validLen($email, INPUT_EMAIL_MIN, INPUT_EMAIL_MAX))
                && (validLen($pass0, INPUT_USER_PASS_MIN, INPUT_USER_PASS_MAX))
                && (validLen($pass1, INPUT_USER_PASS_MIN, INPUT_USER_PASS_MAX))
                && (validLen($keterangan, 0, INPUT_USER_KETERANGAN_MAX))
            )) errorLength();

            $db['query'] = $db['con'] -> prepare('SELECT id FROM users WHERE id=:id AND pass=SHA2(:pass, 224)');
            $db['query'] -> execute([
                ':id' => $id,
                ':pass' => $pass0.KEY_AUTH
            ]);
            if ($db['query'] -> rowCount() < 1) {
                send([
                    'status' => 'error',
                    'desc' => 'Kata sandi lama tidak tepat'
                ]);
            }
            $access_kas = array();
            if (isset($_POST['kas'])) {
                foreach ($_POST['kas'] as $key => $array) {
                    array_push($access_kas, $key);
                }
            }
            $db['query'] = $db['con'] -> prepare('UPDATE users SET nama=:nama, email=:email, pass=sha2(:pass, 224), access=:access, access_kas=:access_kas, keterangan=:keterangan WHERE id=:id');
            if (!$db['query'] -> execute([
                ':nama' => $nama,
                ':email' => $email,
                ':pass' => $pass1.KEY_AUTH,
                ':access' => $access,
                ':access_kas' => json_encode($access_kas),
                ':keterangan' => $keterangan,
                ':id' => $id,
            ])) errorQuery($db['con'] -> errorInfo());
            send([
                'status' => 'success'
            ]);

            break;
//request sko datatable
        case 'dt':
            $draw = $_POST['draw'];
            $start = $_POST['start'];
            if (is_nan($start)) $start = 0;
            $length = $_POST['length'];
            if (is_nan($length)) $length = 10;
            //$search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
            $search = $_POST['search']['value'];
            $order_by = $_POST['order'][0]['column'];
            switch ($order_by) {
                case 1:
                    $order_by = 'email'; break;
                case 3:
                    $order_by = 'keterangan'; break;
                case 4:
                    $order_by = 'access'; break;
                case 5:
                    $order_by = 'id'; break;
                case 0:
                case 2:
                default:
                    $order_by = 'nama'; break;
            }
            $order_as = $_POST['order'][0]['dir'];
            switch ($order_as) {
                case 'desc': $order_as = 'desc'; break;
                case 'asc':
                default: $order_as = 'asc'; break;
            }
            $db['query'] = $db['con'] -> prepare('SELECT COUNT(*) AS total FROM users;');
            $db['query'] -> execute();
            $db['res'] = $db['query'] -> fetchAll();
            $rTotal = $db['res'][0][0];

            $db['query'] = $db['con'] -> prepare('SELECT id, nama, email, pic, access, keterangan FROM users WHERE nama LIKE :search OR email LIKE :search OR access LIKE :search OR keterangan LIKE :search OR id LIKE :search ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length);
            $db['query'] -> execute(array(
                ':search' => '%'. $search .'%'
            ));
            $db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
            $data = array();
            foreach ($db['res'] as $row) {
                array_push($data, array(
                    //'DT_RowId' => 'row_'.$row['id'],
                    'DT_RowAttr' => array(
                        'data-id' => $row['id']
                    ),
                    'nama' => htmlspecialchars($row['nama']),
                    'email' => htmlspecialchars($row['email']),
                    'pic' => htmlspecialchars($row['pic']),
                    'keterangan' => nl2br(htmlspecialchars($row['keterangan'])),
                    'peran' => $row['access'],
                    'id' => $row['id']
                ));
            }
            //$data = $db['res'];
            if ($search == '') $rFilter = $rTotal;
            else $rFilter = $db['query'] -> rowCount();
            send(array(
                'draw' => $draw,
                'recordsTotal' => $rTotal,
                'recordsFiltered' => $rFilter,
                'data' => $data,
                'error' => ''
            ));


            break;
        default:
            errorInvalid();
        }
    } else {
        errorInvalid();
    }

?>
