<?php

namespace Drupal\codimth_batch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form with examples on how to use batch api.
 */
class CodimthBatchForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'codimth_batch_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['delete_node'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Delete All Nodes'),
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $nids = \Drupal::entityQuery('node')->execute();
        $operations = [
            ['delete_nodes_example', [$nids]],
        ];
        $batch = [
            'title' => $this->t('Deleting All Nodes ...'),
            'operations' => $operations,
            'finished' => 'delete_nodes_finished',
        ];
        batch_set($batch);
    }

}