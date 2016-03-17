<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    Jul 28, 2014
 */
class test extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->user->authorize();
        $this->load->library('cimongo/cimongo');
        $this->db = $this->cimongo;
        //$this->dna2 = $this->load->database('dna2', true, true);
        $this->lang->load('library', $this->config->item('language'));
        $this->base_url = base_url();
    }

    function Index() {
        echo "<h2>Pongo empresa de juan-borda a Limon</h2>";
        $idempresa = 3520936162;
        $idu_limon = 1574513092;
        $update = array("owner" => $idu_limon);
        $criteria = array("id" => $idempresa);
        $this->db
                ->where($criteria)
                ->update('container.empresas', $update);

        echo "Mongo ok:<br/>";
        $this->dna2->where(array(
            'id' => $idempresa
        ));
        $this->dna2->update('idsent', array('idu' => $idu_limon));
        echo "DNA2 ok:<br/>";
    }

    function undo() {
        echo "<h2>Pongo empresa de juan-borda a juan-borda</h2>";
        $idempresa = 3520936162;
        $idu_jb = 1;
        $update = array("owner" => $idu_jb);
        $criteria = array("id" => $idempresa);
        $this->db
                ->where($criteria)
                ->update('container.empresas', $update);

        echo "Mongo ok:<br/>";
        $this->dna2->where(array(
            'id' => $idempresa
        ));
        $this->dna2->update('idsent', array('idu' => $idu_jb));
        echo "DNA2 ok:<br/>";
    }

    function fix_assign() {
        $this->load->model('bpm/bpm');
        $query = array(
            'idwf' => 'crefisGral',
            'assign' => array('$type' => 2),
           // 'checkdate' => '2014-10-23 11:36:21'
        );
        $this->db->where($query);
        $rs = $this->db->get('tokens')->result_array();
        echo "encontrados: ".count($rs).'<br/>';
        foreach ($rs as $token) {
            var_dump(
                    $token['_id'],
                    $token['assign']
                    );
            $token['assign'] = array_map(function ($part) {
                return (int) $part;
            }, $token['assign']);
            $this->bpm->save_token($token);
            var_dump($token['assign']);
        }
    }

    function fix_8339() {
        $query = json_decode('{"$and":[{"8339":{"$exists":true}},{"8339":{"$ne":""}}]}', true);
        var_dump($query);
//        exit;
        $this->db->where($query, true);
        $this->db->select();
        $this->db->order_by(array('8339' => 1));
        $rs = $this->db->get('container.proyectos_crefis')->result();

        foreach ($rs as $proj) {
            //$user=$this->user->get_user($proj->idu);
            $query = array('data.Proyectos_crefis.query.id' => $proj->id);
            $this->db->where($query);
            $this->db->select('id');
            $case = $this->db->get('case')->result();
            var_dump($case, $proj->id, $proj->{8339}
                    //,$proj->idu
                    //,$user->name.' '.$user->lastname
            );
            echo '<hr/>';
        }
    }

    function check_case() {
        $SQL = "
            SELECT idsent.id AS id, TF3.valor AS empresa
FROM `td_crefis` AS TF1
INNER JOIN `td_crefis` AS TF2 ON TF1.id = TF2.id
INNER JOIN `td_crefis` AS TF3 ON TF1.id = TF3.id
INNER JOIN idsent ON idsent.id = TF1.id
WHERE TF1.idpreg = 8334
AND TF1.valor = 05
AND TF2.idpreg = 8335
AND TF2.valor = 2014
AND TF3.idpreg = 8325
AND idsent.estado = 'activa'
";
        $rs = $this->dna2->query($SQL);
        foreach ($rs->result() as $row) {
            echo $row->id . '<br>';
            $case = array();
            $this->db->where(array('data.Proyectos_crefis.query.id' => (int) $row->id));
            $this->db->select('id');
//            $this->db->debug=true;
            $case = $this->db->get('case')->result();
            if (count($case)) {
                echo $case[0]->id . '<hr/>';
            } else {
                echo "<h1>NO!</h1><hr/>";
            }
        }
    }

    function fix_evaluador($case = null) {
        $idwf = 'crefisGral';
        $this->load->model('bpm/bpm');
        $this->load->library('parser');
        $this->load->library('bpm/ui');
        $mywf = $this->bpm->load($idwf);
        $wf = $this->bpm->bindArrayToObject($mywf ['data']);
        $lane = $this->bpm->get_shape_byprop(array('name' => 'EVALUADOR TÉCNICO'), $wf);
        $lane = $lane[0];
        //---busco los casos que hayan pasado
        $this->db->where(array('resourceId' => 'oryx_94935482-755B-49C2-8229-A871F575CBD6', 'idwf' => $idwf));
        $tokens = $this->db->get('tokens')->result();
        $cases = array_map(function($token) {
            return array('idcase' => $token->case, 'id' => (property_exists($token, 'data')) ? $token->data['id'] : null);
        }, $tokens);
        foreach ($cases as $case) {
            if ($case['id']) {
                echo '<h1>FIX  ' . $case['idcase'] . ' :: ' . $case['id'] . '</h1>';
                $proy = $this->mongowrapper->db->selectcollection('container.proyectos_crefis')->findOne(array('id' => $case['id']), array('8668'));
                $ideval = $proy['8668'][0];
                $user = $this->user->get_user($ideval);
                echo "EVAL: " . $user->name . ' ' . $user->lastname . '<br/><br/>';
                //--fix lane
                $resourceId = $lane->resourceId;
                $token = $this->bpm->get_token($idwf, $case['idcase'], $resourceId);
                if (count($token['assign']) > 1 or true) {
                    $token['assign'] = array($ideval);
                    $this->bpm->save_token($token);
                    //---Fix comunicacion
                    $token = $this->bpm->get_token($idwf, $case['idcase'], 'oryx_C2EC6376-8EB3-4514-AABA-B4BED6FAB8A1');

                    $token['assign'] = array($ideval);
                    $this->bpm->save_token($token);
                    //--fix tasks
                    foreach ($lane->childShapes as $shape) {
                        if ($shape->stencil->id == 'Task') {
                            $token = $this->bpm->get_token($idwf, $case['idcase'], $shape->resourceId);
                            if (count($token['assign']) > 1) {
                                echo "Fixing:" . $shape->properties->name;
                                $token['assign'] = array($ideval);
                                $this->bpm->save_token($token);
                                echo '<hr/>';
                            }
                        }
                    }
                }//  >1
            }
        }
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
