<?php

namespace Craft;

use Craft\HttpMessages_CraftRequest as Request;

class HttpMessages_ElementsService extends BaseApplicationComponent
{
    /**
     * Get Element Criteria Model
     *
     * @param Request $request      Request
     * @param string  $element_type Element Type
     *
     * @return ElementCriteriaModel
     */
    public function getElementCriteriaModel(Request $request, $element_type = 'Entry')
    {
        $attributes = array_merge($request->getQueryParams(), $request->getAttributes());

        $criteria = craft()->elements->getCriteria($element_type, $attributes);

        if (isset($criteria->page)) {
            $criteria->offset = ($criteria->page - 1) * $criteria->limit;
            unset($criteria->page);
        }

        $slug = $request->getAttribute('slug');
        $element_id = $request->getAttribute('elementId');

        if ($slug) {
            $criteria->slug = $slug;
        }

        if ($element_id) {
            $criteria->id = $element_id;
        }

        if ($slug || $element_id) {
            $criteria->archived = null;
            $criteria->fixedOrder = null;
            $criteria->limit = 1;
            $criteria->localeEnabled = false;
            $criteria->offset = 0;
            $criteria->order = null;
            $criteria->status = null;
            $criteria->editable = null;
        }

        if ($criteria->limit === 'null') {
            $criteria->limit = null;
        }

        return $criteria;
    }

    /**
     * Get Elements
     *
     * @param ElementCriteriaModel $criteria Criteria
     *
     * @return array Elements
     */
    public function getElements(ElementCriteriaModel $criteria)
    {
        return $criteria->find();
    }

    /**
     * Get Element
     *
     * @param ElementCriteriaModel $criteria Criteria
     *
     * @return BaseElementModel Base Element Model
     */
    public function getElement(ElementCriteriaModel $criteria)
    {
        $element = $criteria->first();

        if (!$element) {
            $exception = new HttpMessages_Exception();

            $exception
                ->setStatus(404)
                ->setMessage('Element not found.');

            throw $exception;
        }

        return $element;
    }

    /**
     * Save Element
     *
     * @param array $params Parameters
     *
     * @return BaseElementModel $model
     */
    public function saveElement(BaseElementModel $model, BaseRecord $record, array $attributes = [])
    {
        if (!empty($attributes)) {
            $model->populateModel($attributes);

            $record->setAttributes($attributes);
        }

        $record->validate();

        if ($errors = $record->getErrors()) {
            $exception = new HttpMessages_Exception();

            $exception
                ->setStatus(400)
                ->setMessage('Validation error.')
                ->setErrors($errors);

            throw $exception;
        }

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;

        try {
            if (!craft()->elements->saveElement($model)) {
                $exception = new HttpMessages_Exception();

                $exception
                    ->setStatus(422)
                    ->setMessage('Was not able to save element.');

                throw $exception;
            }

            if (!$record->id) {
                $record->id = $model->id;
            }

            $record->save(false);

            if ($transaction) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }

            throw $e;
        }

        return $model;
    }

    /**
     * Delete Element
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function deleteElement(Request $request)
    {
        craft()->elements->deleteElementById($request->getAttribute('elementId'));
    }

}
