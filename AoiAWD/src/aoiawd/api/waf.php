<?php

namespace aoiawd\api;

use Amp\Http\Status;
use aoiawd\DBHelper;
use aoicommon\api\BaseAPIController;
use MongoDB\BSON\ObjectId;

use const aoiawd\plugin\COLLECTION_NAME;

class WAF extends BaseAPIController
{
    public function createWafRule()
    {
        $name = $this->_POST->name;
        $expression = $this->_POST->expression;
        $response = $this->_POST->response;

        $coll = DBHelper::getDB()->selectCollection(COLLECTION_NAME);
        $insertResult =  $coll->insertOne([
            'name' => $name,
            'expression' => $expression,
            'response' => $response
        ]);
        return $this->response(Status::OK, [
            'status' => true,
            'message' => 'success',
            'data' => [
                'id' => $insertResult->getInsertedId()
            ]
        ]);
    }

    public function deleteWafRule()
    {
        $id = $this->_POST->id;
        $coll = DBHelper::getDB()->selectCollection(COLLECTION_NAME);
        $deleteResult = $coll->deleteOne([
            '_id' => new ObjectId($id)
        ]);
        return $this->response(Status::OK, [
            'status' => true,
            'message' => 'success',
            'data' => [
                'deletedCount' => $deleteResult->getDeletedCount()
            ]
        ]);
    }

    public function getWafRules()
    {
        $id = $this->_GET->id;
        $coll = DBHelper::getDB()->selectCollection(COLLECTION_NAME);
        if (empty($id)) {
            $rules = $coll->find();
            $rules = iterator_to_array($rules);
            return $this->response(Status::OK, [
                'status' => true,
                'message' => 'success',
                'data' => $rules
            ]);
        } else {
            $rule = $coll->findOne([
                '_id' => new ObjectId($id)
            ]);
            return $this->response(Status::OK, [
                'status' => true,
                'message' => 'success',
                'data' => $rule
            ]);
        }
    }
}
