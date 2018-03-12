<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 12.03.18
 * Time: 16:56
 */

namespace Cables\Plugin\Wordpress;


use Cables\Api\CablesApi;
use Cables\Api\Model\Patch;
use WP_List_Table;

class Patches_Table extends WP_List_Table {
    /**
     * @var Patch[]
     */
    private $patches;

    /**
     * @var CablesApi
     */
    private $api;


    /**
     *
     * @var Patch[]
     *
     * My_Patches_Table constructor.
     */
    public function __construct(array $patches, CablesApi $api) {
        parent::__construct();
        $this->api = $api;
        $this->patches = $patches;
    }

    function get_columns(){
        $columns = array(
            'name' => 'Name',
            '_id' => 'ID',
            'tags'      => 'Tags',
            'cachedUsername' => 'User',
            'isPrivate' => 'Private?',
            'imported' => ''
        );
        return $columns;
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->patches;
    }

    function column_default( $item, $column_name ) {
        switch($column_name) {
            case 'picture':
                break;
            case '_id':
                return $item->getId();
                break;
            case 'name':
                $str = '<img height="32" width="32" style="float: left; margin-right: 10px; margin-top: 1px;" class="avatar avatar-32 photo" src="https://cables.gl/api/project/' . $item->getId() . '/screenshot">';
                $str .= '<strong>' . $item->getName() . '</strong>';
                return $str;
                break;
            case 'tags':
                return implode(', ',$item->getTags());
                break;
            case 'cachedUsername':
                return $item->getCachedUsername();
                break;
            case 'isPrivate':
                return $item->getisPrivate();
                break;
            case 'publishedReadable':
                return $item->getPublishedReadable();
                break;
            case 'imported':
                $imported = $this->api->isImported($item);
                if($imported) {
                    return '<a href="'.admin_url( 'admin.php?page=cables_backend_edit_patch&patch=' . $item->getId()).'">edit</a>';
                }else{
                    return '<a href="'.admin_url( 'admin.php?page=cables_backend_import_patch&patch=' . $item->getId()).'">import</a>';
                }
            default:
                return "";
                break;
        }
    }

}
