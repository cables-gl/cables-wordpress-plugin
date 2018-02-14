<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:18
 */

namespace Polyshapes\Plugin\Model;


class Patch {

    private $id;
    private $name;
    private $userId;
    private $tags;
    private $cachedUsername;
    private $isPrivate;
    private $publishedReadable;

    /**
     * Patch constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $jsonshape
     * @return Patch
     */
    public static function fromJson($json): Patch {
        $shape = new Patch();
        $shape->setId($json->_id);
        $shape->setName($json->name);
        $shape->setUserId($json->userId);
        $shape->setTags($json->tags);
        $shape->setCachedUsername($json->cachedUsername);
        $shape->setIsPrivate($json->isPrivate);
        $shape->setPublishedReadable($json->publishedReadable);
        return $shape;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    private function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    private function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    private function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    private function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getCachedUsername() {
        return $this->cachedUsername;
    }

    /**
     * @param mixed $cachedUsername
     */
    private function setCachedUsername($cachedUsername) {
        $this->cachedUsername = $cachedUsername;
    }

    /**
     * @return mixed
     */
    public function getisPrivate() {
        return $this->isPrivate;
    }

    /**
     * @param mixed $isPrivate
     */
    private function setIsPrivate($isPrivate) {
        $this->isPrivate = $isPrivate;
    }

    /**
     * @return mixed
     */
    public function getPublishedReadable() {
        return $this->publishedReadable;
    }

    /**
     * @param mixed $publishedReadable
     */
    private function setPublishedReadable($publishedReadable) {
        $this->publishedReadable = $publishedReadable;
    }


}
