<?php
/**
 * Created by PhpStorm.
 * User: Enner
 * Date: 6/26/2018
 * Time: 8:36 AM
 */

namespace App\Helpers;


class BuilderTable
{
    private  $containerHeaders;
    private  $containerRows;
    public function newTable():BuilderTable{
        $this->containerHeaders = new \stdClass;
        return $this;
    }

    public function addHeader($name,$label) :BuilderTable{
        $hdr = new \stdClass;
        $hdr->name = $name;
        $hdr->label = $label;
        $this->containerHeaders->headers[] = $hdr;
        return $this;
    }

    public function getHeaders(){
        return $this->containerHeaders->headers;
    }

    public function buildRows($entities):BuilderTable{
        $this->containerRows = new \stdClass;
        $this->containerRows->rows = [];
        foreach ($entities as $entity) {
            $row = new \stdClass;
            //array_merge($row,$entity);
            $this->containerRows->rows[] =$entity;
        }
        return $this;
    }

    public function buildTable(){
        $table = new \stdClass;

        $table->headers = $this->getHeaders();
        $table->rows =  $this->containerRows->rows;
        return $table;
    }
}