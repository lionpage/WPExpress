<?php
/**
 * Developer: Page Carbajal (https://github.com/Page-Carbajal)
 * Date: 9/29/15, 6:37 PM
 * Generator: PhpStorm
 */

namespace WPExpress;


class Query
{

    protected $parameters;
    protected $metaConditions;
    protected $metaRelation;
    protected $taxonomyConditions;
    protected $taxonomyRelation;

    public function __construct()
    {
        $this->metaConditions = array();
        $this->taxonomyConditions = array();
        $this->metaRelation = $this->taxonomyRelation = 'AND';
        $this->parameters = array( 'showposts' => 10 );
    }

    private function addParameter( $parameter, $value, $condition = null ){
        $this->parameters[$parameter] = $value;
    }


    // Set the limit of posts to retrieve;

    public function all(){
        $this->limit(-1);
        return $this;
    }

    public function limit($total){
        if( is_int( $total ) ){
            $this->parameters['showposts'] = intval( $total );
        }
        return $this;
    }

    // Post metadata methods

    public function meta($field, $value, $operator = null)
    {
        $compare = '';
        switch( $operator ){
            case "like":
                $compare = "like";
                break;
            default:
                $compare = "=";
                break;
        }

        $this->metaConditions[] =array(
            'key' => $field,
            'value' => $value,
            'compare' => $compare,
        );

        return $this;
    }

    public function metaRelation($useAND = true)
    {
        if( $useAND ){
            $this->metaRelation = 'AND';
        } else{
            $this->metaRelation = 'OR';
        }
        return $this;
    }


    // Post Methods
//    public function ID($ID)
//    {
//        $this->addParameter( 'p', $ID );
//        return $this;
//    }

    public function postType($type)
    {
        $this->addParameter( 'post_type', $type );
        return $this;
    }

    // Process Values
    private function buildArguments(){

        if( !empty($this->metaConditions) ){
            $this->parameters['meta_query'] = array_merge( array( 'relation' => $this->metaRelation ), $this->metaConditions);
        }

        return $this->parameters;
    }

    public function get()
    {
        $args = $this->buildArguments();
        $query = new \WP_Query( $args );
        $posts = $query->get_posts();
        wp_reset_postdata();
        return $posts;
    }

    // Static methods

    // Instance methods
    // This methods allow for a simple understanding of the WP_Query Abstraction Layer

    /**
     * Returns an new instance to the class Query. Sets the postType property to 'post'. Use method get to return values.
     *
     * @return Query
     */
    public static function Posts()
    {
        $query = new self();
        return $query->postType('post');
    }

    /**
     * Returns an new instance to the class Query. Sets the postType property to 'post'. Use method get to return values.
     *
     * @param $postType. The post type to be queried. 'post', 'page', etc...
     * @return Query
     */
    public static function Custom($postType)
    {
        $query = new self();
        return $query->postType( $postType );
    }

}
