<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Kpi_individual extends Base_controller {

    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('goals_settings/Kpi_individual_repository');
        $this->repository = new Kpi_individual_repository();

        $this->is_logged_in();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'KPI Individual',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css',
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                'admin/goals-settings/kpi-individual/kpi-individual.js',
                'admin/goals-settings/kpi-individual/list.js',
                'admin/goals-settings/kpi-individual/add.js',
                'admin/goals-settings/kpi-individual/target-actual.js',
                'admin/goals-settings/kpi-individual/submit.js',
            ],
        ]);
        $this->template->load('admin/layout', 'admin/goals_settings/kpi_individual/index', $data);
    }
    
    public function get_employee() {
        $data = $this->global;

        $employee = false;

        if ($data['employee_id']) {
            $employee = $this->repository->get_employee_by_employee_id($data['employee_id']);
        }

        $message = $employee ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$employee ? 'success' : 'error'}($message, $employee);
    }

    ##### Store Update KPI

    public function store_update_kpi() {
        $mode = $this->input->post('mode');
        if (!$this->validate_input_store_update_kpi()) {
            $this->handle_validation_error_store_update_kpi();
            return;
        }

        $data = $this->collect_input_store_update_kpi();
        if ($mode == 'add') {
            $exists = $this->repository->exists_kpi($data);
            
            if ($exists['is_exists']) {
                $this->json_response->error(
                    'Data for the ' . $exists['data']->kpi . 
                    ' KPI is already in use.'
                );
                return;
            }
            $this->save_kpi($data);
        } else {
            $exists = $this->repository->exists_kpi($data);
            $unique = $this->repository->unique_kpi($data);
            
            if ($exists['is_exists'] && $unique['is_unique']) {
                $this->json_response->error(
                    'Data for the ' . $exists['data']->kpi . 
                    ' KPI is already in use.'
                );
                return;
            }
            $this->update_kpi($data);
        }
    }

    private function validate_input_store_update_kpi() {
        $this->form_validation->set_rules('kpi_id', 'KPI', 'required|trim');
        $this->form_validation->set_rules('weight', 'Weight', 'required|trim');
        return $this->form_validation->run();
    }

    private function handle_validation_error_store_update_kpi() {
        $errors = $this->collect_form_errors_store_update_kpi();
        $this->json_response->error('Failed to collect data.', $errors);
    }

    private function collect_form_errors_store_update_kpi() {
        return [
            'kpi_id' => form_error('kpi_id'),
            'weight' => form_error('weight')
        ];
    }

    private function collect_input_store_update_kpi() {
        $input_data = [
            'id' => $this->input->post('id'),
            'year_period_id' => $this->input->post('year_period_id'),
            'pa_individual_id' => $this->input->post('pa_individual_id'),
            'kpi_id' => $this->input->post('kpi_id'),
            'weight' => $this->input->post('weight'),
        ];

        return $input_data;
    }

    private function save_kpi($data) {
        $store = $this->repository->store_kpi($data);
        $message = $store ? 'Data Successfully Saved.' : 'Failed to save data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_kpi($data) {
        $store = $this->repository->update_kpi($data);
        $message = $store ? 'Data Successfully Updated.' : 'Failed to update data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    public function delete_kpi() {
        $id = $this->input->post('id');
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
    
        $delete = $this->repository->delete_kpi($id);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }

    public function submit_kpi() {
        $input_data = [
            'pa_individual_id' => $this->input->post('pa_individual_id'),
            'year_period_id' => $this->input->post('year_period_id'),
            'is_submit' => $this->input->post('is_submit'),
        ];
        if (!isset($input_data['pa_individual_id']) || !isset($input_data['year_period_id'])) {
            $this->json_response->error('Unit or Year Period is required.');
            return;
        }
    
        $submit = $this->repository->submit_kpi($input_data);
        $message = $submit ? 'Data successfully submitted.' : 'Failed to submit data.';
        $this->json_response->{$submit ? 'success' : 'error'}($message, $submit);
    }

    ##### End Store Update KPI

    ##### Add Edit Target

    public function add_edit_target() {
        $target_id = $this->input->post('target_id');
        if (!$this->validate_input_add_edit_target()) {
            $this->handle_validation_error_add_edit_target();
            return;
        }

        $data = $this->collect_input_add_edit_target();
        if (!$target_id) {
            $this->save_target($data);
        } else {
            $this->update_target($data);
        }
    }

    private function validate_input_add_edit_target() {
        return true;
    }

    private function handle_validation_error_add_edit_target() {
        $errors = $this->collect_form_errors_add_edit_target();
        $this->json_response->error('Failed to collect data.', $errors);
    }

    private function collect_form_errors_add_edit_target() {
        return [];
    }

    private function collect_input_add_edit_target() {
        $data_target = [
            '1' => $this->input->post('month_1'),
            '2' => $this->input->post('month_2'),
            '3' => $this->input->post('month_3'),
            '4' => $this->input->post('month_4'),
            '5' => $this->input->post('month_5'),
            '6' => $this->input->post('month_6'),
            '7' => $this->input->post('month_7'),
            '8' => $this->input->post('month_8'),
            '9' => $this->input->post('month_9'),
            '10' => $this->input->post('month_10'),
            '11' => $this->input->post('month_11'),
            '12' => $this->input->post('month_12')
        ];
        
        $target = json_encode($data_target);

        $input_data = [
            'kpi_individual_id' => $this->input->post('kpi_individual_id'),
            'target' => $target
        ];

        if (empty($this->input->post('target_id'))) {
            if ($this->global['vendor_id']) {
                $input_data['created_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
                $input_data['updated_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
            }
        } else {
            $input_data['id'] = $this->input->post('target_id');
            if ($this->global['vendor_id']) {
                $input_data['updated_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
            }
        }
        return $input_data;
    }

    private function save_target($data) {
        $store = $this->repository->store_target($data);
        $message = $store ? 'Data Successfully Saved.' : 'Failed to save data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_target($data) {
        $store = $this->repository->update_target($data);
        $message = $store ? 'Data Successfully Updated.' : 'Failed to update data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    ##### End Add Edit Target

    ##### Submit Target

    public function submit_target() {
        $pa_individual_id = $this->input->post('pa_individual_id');
        $term_and_conditions = $this->input->post('term_and_conditions');
        $id = $this->input->post('id');

        if (!$pa_individual_id) {
            return $this->json_response->error('PA Individual ID is required.');
        }

        if (!$term_and_conditions) {
            return $this->json_response->error('Term and Condition is required.');
        }

        $data = [
            'self_submit' => 1,
            'puk_1_status' => 0,
            'puk_2_status' => 0,
            'month_period' => get_current_month(),
            'month_periods' => json_encode([get_current_month()]),
            'self_submit_time' => date('Y-m-d H:i:s')
        ];

        if (!empty($id)) {
            $data['id'] = $id;
            $this->update_submit_target($data);
        } else {
            $data = array_merge($data, [
                'pa_individual_id' => $pa_individual_id,
                'term_and_conditions' => $term_and_conditions,
                'puk_1_status' => 0,
                'puk_2_status' => 0,
                'puk_1_unit' => $this->input->post('unit_approval_1_id') ?: null,
                'puk_1_position' => $this->input->post('position_approval_1_id') ?: null,
                'puk_2_unit' => $this->input->post('unit_approval_2_id') ?: null,
                'puk_2_position' => $this->input->post('position_approval_2_id') ?: null,
                'created_by' => $this->global['vendor_id'],
                'updated_by' => $this->global['vendor_id']
            ]);

            if ($this->repository->exists_submit_target($data)['is_exists']) {
                return $this->json_response->error('Data is already submitted.');
            }

            $this->save_submit_target($data);
        }
    }

    private function save_submit_target($data) {
        $store = $this->repository->store_submit_target($data);
        $message = $store ? 'Data Successfully Submitted.' : 'Failed to submit data.';
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_submit_target($data) {
        $store = $this->repository->update_submit_target($data);
        $message = $store ? 'Data Successfully Submitted.' : 'Failed to submit data.';
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    ##### End Submit Target

    public function get_year_period_options() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $result = $this->repository->get_year_period_options($search, $page);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->year_period], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_employee_options() {
        $data = $this->global;
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        if ($data['employee_id']) {
            $individual = $this->repository->get_employee_options_by_employee_id($search, $page, $data['employee_id']);
        } else {
            $individual = $this->repository->get_employee_options($search, $page);
        }
        $result = $individual;
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id_peg, 'text' => $item->nama], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_kpi_options_by_year_period_id() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $year_period_id = $this->input->get('year_period_id');
        $result = $this->repository->get_kpi_options_by_year_period_id($search, $page, $year_period_id);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->kpi], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_position_unit_placement_unit_by_employee_id($employee_id) {
        $data = $this->get_position_unit_placement_unit_employee($employee_id);
        $position = $this->repository->get_position_by_id($data['position_id']);
        $unit = $this->repository->get_unit_by_id($data['unit_id']);
        $placement_unit = $this->repository->get_unit_by_id($data['placement_unit_id']);
        
        $this->json_response->success('Data successfully fetched.', [
            'npp' => $data['npp'],
            'position' => $position,
            'unit' => $unit,
            'placement_unit' => $placement_unit,
            'description' => $data['description']
        ]);
    }

    private function get_position_unit_placement_unit_employee($employee_id) {
        $npp = NULL;
        $position_id = NULL;
        $unit_id = NULL;
        $placement_unit_id = NULL;
        $description = NULL;

        $temp_position_hist = $this->repository->get_temp_position_hist_by_employee_id($employee_id);

        if ($temp_position_hist) {
            $npp = $temp_position_hist->nrik;
            $position_id = $temp_position_hist->id_jabatan_diganti;
            $unit_id = $temp_position_hist->id_unit_kerja;
            $placement_unit_id = $temp_position_hist->ditempatkan_diganti;
            $description = 'Pelaksana Jabatan s/d ' . date('d F Y', strtotime($temp_position_hist->tgl_selesai));
        } else if (($temp_assignment_hist = $this->repository->get_temp_assignment_hist_by_employee_id($employee_id))) {
            $npp = $temp_assignment_hist->nrik;
            $position_id = $temp_assignment_hist->tmp_jabatan;
            $unit_id = $temp_assignment_hist->id_uker_penempatan;
            $placement_unit_id = $temp_assignment_hist->id_uker_ndpenugasan;
            $description = 'Nota Dinas Penugasan s/d ' . date('d F Y', strtotime($temp_assignment_hist->tgl_selesai));
        } else if (($position_hist = $this->repository->get_position_hist_by_employee_id($employee_id))) {
            $npp = $position_hist->nrik;
            $position_id = $position_hist->id_jabatan;
            $unit_id = $position_hist->id_unit_kerja;
            $placement_unit_id = $position_hist->ditempatkan_di;
        }

        return [
            'npp' => $npp,
            'position_id' => $position_id,
            'unit_id' => $unit_id,
            'placement_unit_id' => $placement_unit_id,
            'description' => $description
        ];
    }

    public function get_goals_settings() {
        $year_period_id = $this->input->post('year_period_id');
        $employee_id = $this->input->post('employee_id');
        $data = $this->repository->get_pa_individual($year_period_id, $employee_id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_kpi_individual_by_pa_id($pa_id) {
        $kpi = $this->repository->get_kpi_individual_by_pa_id($pa_id);
        $message = $kpi ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$kpi ? 'success' : 'error'}($message, $kpi);
    }

    public function get_kpi_individual_target_by_pa_individual_id_year_period_id() {
        $data = $this->input->post();
        $kpi = $this->repository->get_kpi_individual_target_by_pa_individual_id_year_period_id($data);
        $this->json_response->success('Data successfully fetched.', $kpi);
    }

    public function get_target_by_id($id) {
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $data = $this->repository->get_target_by_id($id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_approval_by_goals_settings() {
        $data = $this->input->post();
        $unit_id = $data['placement_unit_id'];
        $position_id = $data['position_id'];

        $approval_1 = $this->get_approval_data($unit_id, $position_id, 'unit_approval_1_id','position_approval_1_id', 'position_approval_1_name', 'approval_1_name', 'approval_1_npp', 'unit_approval_1_name');
        $approval_2 = $this->get_approval_data($unit_id, $position_id, 'unit_approval_2_id', 'position_approval_2_id', 'position_approval_2_name', 'approval_2_name', 'approval_2_npp', 'unit_approval_2_name');

        $approval = [
            'approval_1' => $approval_1,
            'approval_2' => $approval_2
        ];

        $this->json_response->success('Data successfully fetched.', $approval);
    }

    private function get_approval_data($unit_id, $position_id, $approval_unit_id_field, $approval_position_id_field, $position_name_field, $name_field, $npp_field, $unit_name_field) {
        $approval_histories = [
            $this->repository->get_approval_by_unit_id_position_id_in_hist_pelaksana_jabatan($unit_id, $position_id),
            $this->repository->get_approval_by_unit_id_position_id_in_hist_ndpenugasan($unit_id, $position_id),
            $this->repository->get_approval_by_unit_id_position_id_in_hist_jabatan($unit_id, $position_id)
        ];

        $approval_data = [];

        foreach ($approval_histories as $approval_history) {
            foreach ($approval_history as $item) {
                if ($item->{$approval_unit_id_field} && $item->{$approval_position_id_field}) {
                    $approval_data[] = [
                        $approval_unit_id_field => $item->{$approval_unit_id_field},
                        $approval_position_id_field => $item->{$approval_position_id_field},
                        $position_name_field => $item->{$position_name_field},
                        $name_field => $item->{$name_field},
                        $npp_field => $item->{$npp_field},
                        $unit_name_field => $item->{$unit_name_field}
                    ];
                }
            }
            if (!empty($approval_data)) {
                break;
            }
        }

        return $approval_data;
    }

    public function get_approval_target() {
        $data = $this->input->post();
        $pa_individual_id = $data['pa_individual_id'];
        $month_period = get_current_month();
        $approval = $this->repository->get_approval_target_by_pa_individual_id($pa_individual_id, $month_period);
        $message = $approval ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$approval ? 'success' : 'error'}($message, $approval);
    }

    public function get_kpi_by_id($id = '') {
        if (!$id) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $data = $this->repository->get_kpi_by_id($id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }
    
    public function export_pdf() {
        // $data = $this->input->post();
        // $pa_individual_id = $data['pa_individual_id'];

        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('KPI Information');
        $pdf->SetSubject('KPI Information');
        $pdf->SetKeywords('TCPDF, PDF, KPI, information');

        // Set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Add content
        $html = '
        <h1>KPI Information</h1>
        <table border="1" cellpadding="4">
            <tr>
                <th>Year Period</th>
                <td>2024</td>
            </tr>
            <tr>
                <th>Employee</th>
                <td>A. Rahman Lestari Bunga</td>
            </tr>
            <tr>
                <th>KPI</th>
                <td>STL/SM/SET/01/2022</td>
            </tr>
            <tr>
                <th>Position</th>
                <td>Pemimpin Operasional Cabang Pembantu, Kelas 3</td>
            </tr>
            <tr>
                <th>Unit</th>
                <td>Cabang Pembantu, Cakranegara</td>
            </tr>
            <tr>
                <th>Placement Unit</th>
                <td>Cabang Pembantu, Cakranegara</td>
            </tr>
        </table>
        <h2>Goals Settings</h2>
        <table border="1" cellpadding="4">
            <tr>
                <th>Position (Jabatan)</th>
                <th>Unit</th>
                <th>Placement Unit (Penempatan Unit)</th>
                <th>Month Period</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>Pemimpin Operasional Cabang Pembantu, Kelas 3</td>
                <td>Cabang Pembantu, Cakranegara</td>
                <td>Cabang Pembantu, Cakranegara</td>
                <td>January to December</td>
                <td></td>
            </tr>
        </table>
        <h2>KPI</h2>
        <table border="1" cellpadding="4">
            <tr>
                <th>KPI</th>
                <th>Percentage</th>
            </tr>
            <tr>
                <td>Pemimpin Operasional Cabang Pembantu, Kelas 3</td>
                <td>100.00%</td>
            </tr>
        </table>
        <table border="1" cellpadding="4">
            <tr>
                <th>Position</th>
                <td>Pemimpin Operasional Cabang Kelas 1</td>
                <td>100.00%</td>
            </tr>
        </table>
        <table border="1" cellpadding="4">
            <tr>
                <th>KPI</th>
                <th>Measurement</th>
                <th>Target</th>
                <th>Actual</th>
                <th>Counter</th>
                <th>Polarization</th>
                <th>Weight</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td>Proses Operasional</td>
                <td>Bilangan</td>
                <td>Target</td>
                <td>Aktual</td>
                <td>LAST_</td>
                <td>Maximum</td>
                <td>70</td>
                <td></td>
            </tr>
            <tr>
                <td>Training Mandays</td>
                <td>Percentage %</td>
                <td>Target</td>
                <td>Aktual</td>
                <td>LAST_</td>
                <td>Maximum</td>
                <td>30</td>
                <td></td>
            </tr>
        </table>
        <h2>Target Submit Information</h2>
        <table border="1" cellpadding="4">
            <tr>
                <th>Unit</th>
                <th>Position</th>
                <th>Employee</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>Unit 1</td>
                <td>Position 1</td>
                <td>Employee 1</td>
                <td></td>
            </tr>
        </table>
        ';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        
        
        // Menyimpan file PDF
        $pdf->Output('contoh.pdf', 'I');
    }

    public function export_excel() {
        // Load library PHPSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator('Your Name')
            ->setLastModifiedBy('Your Name')
            ->setTitle('Export Excel')
            ->setSubject('Export Excel')
            ->setDescription('Export Excel using PHPSpreadsheet')
            ->setKeywords('export excel phpspreadsheet')
            ->setCategory('Export');

        // Add some data
        $sheet->setCellValue('A1', 'Proses Operasional');
        $sheet->setCellValue('B1', 'Bilangan');
        $sheet->setCellValue('C1', 'Target');
        $sheet->setCellValue('D1', 'Aktual');
        $sheet->setCellValue('E1', 'LAST_');
        $sheet->setCellValue('F1', 'Maximum');
        $sheet->setCellValue('G1', '70');

        $sheet->setCellValue('A2', 'Training Mandays');
        $sheet->setCellValue('B2', 'Percentage %');
        $sheet->setCellValue('C2', 'Target');
        $sheet->setCellValue('D2', 'Aktual');
        $sheet->setCellValue('E2', 'LAST_');
        $sheet->setCellValue('F2', 'Maximum');
        $sheet->setCellValue('G2', '30');

        // Add Target Submit Information
        $sheet->setCellValue('A4', 'Target Submit Information');
        $sheet->setCellValue('A5', 'Unit');
        $sheet->setCellValue('B5', 'Position');
        $sheet->setCellValue('C5', 'Employee');
        $sheet->setCellValue('D5', 'Action');

        $sheet->setCellValue('A6', 'Unit 1');
        $sheet->setCellValue('B6', 'Position 1');
        $sheet->setCellValue('C6', 'Employee 1');
        $sheet->setCellValue('D6', '');

        // Rename worksheet
        $sheet->setTitle('Export Excel');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_excel.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}