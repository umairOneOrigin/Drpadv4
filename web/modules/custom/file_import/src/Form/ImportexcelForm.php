<?php

namespace Drupal\file_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Drupal\taxonomy\Entity\Term;



class ImportexcelForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'ImportexcelForm';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form = array(
            '#attributes' => array('enctype' => 'multipart/form-data'),
        );

        $form['file_upload_details'] = array(
            '#markup' => '<b>The File</b>',
        );

        $validators = array(
            'file_validate_extensions' => array('xlsx'),
        );

        $form['excel_file'] = array(
            '#type' => 'managed_file',
            '#name' => 'excel_file',
            '#title' => 'File *',
            '#size' => 30, //doubt
            '#description' => 'Excel format only',
            '#upload_validators' => $validators,
            '#upload_location' => 'public://content/excel_files/',
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary',
        );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if ($form_state->getValue('excel_file') == NULL) {
            $form_state->setErrorByName('excel_file', $this->t('upload Proper File!'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $file = \Drupal::entityTypeManager()->getStorage('file')
            ->load($form_state->getValue('excel_file')[0]);

        // dump($file);----------------------------

        $full_path = $file->get('uri')->value;
        $file_name = basename($full_path); //doubt

        // dump($file_name);

        try {
            $inputFileName = \Drupal::service('file_system')->realpath('public://content/excel_files/' . $file_name);

            $spreadsheet = IOFactory::load($inputFileName);

            $sheetData = $spreadsheet->getActiveSheet();

            $rows = array();

            foreach ($sheetData->getRowIterator() as $row) {
                // echo "<pre>";
                //     print_r($row);
                // echo "</pre>";
                // exit;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $cells = [];

                foreach ($cellIterator as $cell) {

                    $cells[] = $cell->getValue();
                }
                $rows[] = $cells;
            }
            // echo "<pre>";
            // print_r($rows);
            // echo "</pre>";
            // exit;              here all the arrays and data is displayed

            array_shift($rows); // shifting to next array 
            $count = count($rows[3]);

            // dump($rows);
            

            // for ($x = 0; $x <= $count; $x++) {

            //     if ($x <= 3) {
                    // dump($x);
                    // exit;
                    $abc = array_chunk($rows, 50);

                    $size =sizeof($abc);
                    
                    // dump($rows);
                    // dump(count($abc));
                    dump($size);
                    // dump($abc[0]);
                    // dump($abc[0][0]);
                    // exit;

                    // $exe = array();

                for ($x = 0; $x < $size; $x++){

                    // $exe[] = $rows[$x];
                    // if(($x+1) % 3 == 0){

                        $operations = [
                            ['create_nodes', [$abc[$x]]],
                        ]; 

                    // }
                    // dump($abc[$x]);
                }

                $batch = [
                    'title' => $this->t('First 3 nodes creation ...'),
                    'operations' => $operations,
                    'finished' => 'completion',
                ];

                batch_set($batch);
                // exit;
                // dump();
                
                // $operations = [
                //     ['create_nodes', [$rows[$x]]],
                // ];
                    

                    
                    // dump($operations);
                // }
                // \Drupal::messenger()->addMessage('First 3 nodes are created');
                    
                    // exit;

                    // \Drupal::messenger()->addMessage('First 3 nodes are created');
                    // } elseif ($x > $rows[3] && $x <= $rows[6]) {
                    //     $operations = [
                    //         ['create_nodes', [$rows[$x]]],
                    //     ];

                    //     $batch = [
                    //         'title' => $this->t('second 3 nodes creation ...'),
                    //         'operations' => $operations,
                    //         'finished' => 'completion',
                    //     ];

                    //     batch_set($batch);

                    //     \Drupal::messenger()->addMessage('second 3 nodes are created');
                    // } else {
                    //     \Drupal::messenger()->addMessage('End of File!');
                    // }
            //     }
            // }




            // $operations = [
            //     ['create_nodes', [$rows]],
            // ];

            // $batch = [
            //     'title' => $this->t('Creating All Nodes ...'),
            //     'operations' => $operations,
            //     'finished' => 'completion',
            // ];

            // batch_set($batch);
            // foreach ($rows as $row) {

            //     // $count++;
            //     // if ($count % 50 == 0) {
            //     //     \Drupal::messenger()->addMessage('50 nodes have been created...count-'.$count);
            //     // }


            //     $values = \Drupal::entityQuery('node')->condition('title', $row[0])->execute();
            //     // dump($values);
            //     // exit;
            //     $node_not_exists = empty($values);

            //     if ($node_not_exists) {
            //         /*if node does not exist create new node*/

            //         $term = \Drupal::entityTypeManager()
            //             ->getStorage('taxonomy_term')
            //             ->loadByProperties(['name' => $row[1]]);

            //         $node = \Drupal::entityTypeManager()->getStorage('node')->create([
            //             'type'       => 'news', //===here news is the content type mechine name
            //             'title'      => $row[0],
            //             'field_category'   => $term,
            //             'field_description' => $row[2],
            //             'field_images' => $row[3],
            //         ]);
            //         // dump($node);
            //         // exit;
            //         $node->save();
            //     } else {

            //         $nid = reset($values);

            //         // $new_term = Term::create([
            //         //     'type' => 'news',
            //         //     'field_category' => $row[1],
            //         //     'vid' => 'category_',
            //         // ]);
            //         // $new_term->save();
            //         // $bac = \Drupal::entityTypeManager()->getStorage('node');
            //         // $bac->get('field_category')->referencedEntities();
            //         // dump($bac);
            //         // exit;

            //         // $abc = \Drupal::entityTypeManager()->get('target_id')->value;

            //         $node = \Drupal\node\Entity\Node::load($nid);

            //         $term = \Drupal::entityTypeManager()
            //             ->getStorage('taxonomy_term')
            //             ->loadByProperties(['name' => $row[1]]);
            //         // dump($term);
            //         // exit;
            //         // $node = \Drupal::routeMatch()->getParameter('node');
            //         // dump($node);
            //         // exit;
            //         // $terms = $node->get('field_category')->referencedEntities();
            //         // dump($terms);
            //         // $tid = $terms->id();
            //         // dump($tid);
            //         // $cat = $node->get('field_category')->getValue();
            //         // dump($cat);

            //         $node->setTitle($row[0]);
            //         $node->set("field_category", $term);
            //         $node->set("field_description", $row[2]);
            //         $node->set("field_images", $row[3]);
            //         // dump($node);
            //         // exit;
            //         $node->save();
            //     }

            //     // dump($node);
            //     // exit;
            //     \Drupal::messenger()->addMessage('File imported Successfully!');
            // }


        } catch (\Exception $e) {
            \Drupal::logger('type')->error($e->getMessage());
            // return $e->getMessage();
        }
    }
}
