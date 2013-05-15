<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of meeting
 *
 * @author juanb
 */
if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Meeting extends CI_Model {

        function __construct() {
                parent::__construct();
                $this->idu = $this->session->userdata('iduser');

                $this->load->library('cimongo/cimongo');
                $this->cimongo->close = function() {
                                
                        };
                $this->db = $this->cimongo;
                $this->frameEvent = '7403';
                $this->frameBusiness = '7466';
                $this->orderby = array('1693'=>'asc');
                $this->container_empresas = 'container.ronda1';
                $this->container_import = 'container.ronda';

                //$this->load->database();
        }

        function store_tables($arr) {
                $this->db->delete('agenda_table');
                $this->db->insert('agenda_table', $arr);
        }

        function store_agenda_business($arr) {
                $this->db->delete('agenda_business');
                $this->db->insert('agenda_business', $arr);
        }

        function store_wishlist($arr) {
                $this->db->delete('agenda_wishlist');
                $this->db->insert('agenda_wishlist', $arr);
        }

        function find_business($query) {
                $this->db->where(array('1715' => $query));
                $rs = $this->db->get($this->container_empresas)->result_array();
                if ($rs)
                        return_bytes($rs);
        }

        function get_name($id) {
                $name = 'XXX ' . (float) $id;
                $this->db->where(array('id' => (float) $id));
                $this->db->select('1693', 'id');
                $rs = $this->db->get($this->container_empresas)->result_array();
                if ($rs)
                        $name = (isset($rs[0]['1693'])) ? $rs[0]['1693'] : $rs[0]['id'];
                return utf8_encode(trim($name));
        }

        function get_data($id) {
                $this->db->where(array('id' => (float) $id));
                $rs = $this->db->get($this->container_empresas)->result_array();
                if ($rs)
                        return $rs[0];
        }

        function get_tables() {
                $rs = $this->db->get('agenda_table')->result_array();
                if ($rs)
                        return $rs[0];
        }

        function get_agenda_business() {
                $rs = $this->db->get('agenda_business')->result_array();
                if ($rs)
                        return $rs[0];
        }

        function get_wishlist() {
                $rs = $this->db->get('agenda_wishlist')->result_array();
                if ($rs)
                        return $rs[0];
        }

        function get_empresa($id) {
                $this->db->where(array('id' => (float) $id));
                $rs = $this->db->get($this->container_empresas)->result_array();
                return $rs;
        }

        function accredit($id) {
                $data = array('accredited' => 1);
                $this->db->where(array('id' => (float) $id));
                $this->db->update($this->container_empresas, $data);
        }

        function get_accredited() {
                $this->db->where(array('accredited' => 1, $this->frameEvent => '1'));
                $this->db->select('id');
                $this->db->order_by($this->orderby);
                $rs = $this->db->get($this->container_empresas)->result_array();
                if ($rs) {
                        return $rs;
                }
        }

        function get_registered() {
                $this->db->where(array($this->frameEvent => '1'));
                $this->db->order_by($this->orderby);
                $rs = $this->db->get($this->container_empresas)->result_array();
                if ($rs)
                        return $rs;
        }

        function get_empresa_cuit($cuit) {

                $this->db->where(array('1695' => $cuit, $this->frameEvent => '1'));
                $rs = $this->db->get($this->container_empresas)->result_array();
                if ($rs) {
                        return $rs[0];
                } else {
                        return array();
                }
        }

        function get_total_business() {
                $query = array($this->frameEvent => '1');
                $this->db->select('id', $this->frameBusiness);
                $this->db->where($query);
                $result = $this->db->get($this->container_empresas)->result_array();

                return count($result);
        }

        function load_business() {
                //---add criteria here
                $whishes = array();
                $business = array();
                $arr = array();
                $query = array($this->frameEvent => '1');
                $query = array($this->frameEvent => '1', 'accredited' => 1);
                $this->db->select('id', $this->frameBusiness);
                $this->db->where($query);
                $result = $this->db->get($this->container_empresas)->result_array();
                $rs = $this->get_accredited();
                $accredited = array();
                if ($rs) {
                        foreach ($rs as $b1)
                                $accredited[] = $b1['id'];
                }
                foreach ($result as $emp) {
                        if (isset($emp[$this->frameBusiness])) {
                                //----devuelvo solo las empresas que están acreditadas
                                $arr[(float) $emp['id']] = array_intersect($accredited, $emp[$this->frameBusiness]);
                        }
                }
                return array_filter($arr);
        }

        function merge_data() {
                $rs = $this->db->get($this->container_import)->result_array();
                $imports = 0;
                $updates = 0;
                foreach ($rs as $importb1) {
                        $b1 = $this->get_data($importb1['id']);
                        if ($b1) {
                                //----Existe la empresa
                                //----Importo los deseos
                                if (isset($importb1['7466'])) {
                                        $this->db->where(array('id' => $b1['id']))->set(array('7466' => $importb1['7466']))->update($this->container_empresas);
                                        $updates++;
                                }
                        } else {
                                //----No existe y la agrego
                                $this->db->insert($this->container_empresas, $importb1);
                                $imports++;
                        }
                }
                return array(
                    'imports' => $imports,
                    'updates' => $updates
                );
        }

}