<?php

class PVExtra
{
    /** @var modX $modx */
    public $modx;

    /** @var pdoFetch $pdoTools */
    public $pdo;

    /** @var array() $config */
    public $config = array();
    
    public $timings = [];
    protected $start = 0;
    protected $time = 0;
    public $gtsShop;
    public $getTables;
    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH . 'components/pvextra/';
        // $assetsUrl = MODX_ASSETS_URL . 'components/pvextra/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            // 'processorsPath' => $corePath . 'processors/',
            // 'customPath' => $corePath . 'custom/',

            // 'connectorUrl' => $assetsUrl . 'connector.php',
            // 'assetsUrl' => $assetsUrl,
            // 'cssUrl' => $assetsUrl . 'css/',
            // 'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->modx->addPackage('pvextra', $this->config['modelPath']);
        $this->gtsShop = $modx->getService("gtsShop","gtsShop",
            MODX_CORE_PATH."components/gtsshop/model/",[]);
        //$this->modx->lexicon->load('pvextra:default');
        $gettables_core_path = $this->modx->getOption('gettables_core_path',null, MODX_CORE_PATH . 'components/gettables/core/');
        $gettables_core_path = str_replace('[[+core_path]]', MODX_CORE_PATH, $gettables_core_path);
        if ($this->modx->loadClass('gettables', $gettables_core_path, false, true)) {
            $this->getTables = new getTables($this->modx, []);
        }

        if ($this->pdo = $this->modx->getService('pdoFetch')) {
            $this->pdo->setConfig($this->config);
        }
        $this->timings = [];
        $this->time = $this->start = microtime(true);
    }
    /**
     * Add new record to time log
     *
     * @param $message
     * @param null $delta
     */
    public function addTime($message, $delta = null)
    {
        $time = microtime(true);
        if (!$delta) {
            $delta = $time - $this->time;
        }

        $this->timings[] = array(
            'time' => number_format(round(($delta), 7), 7),
            'message' => $message,
        );
        $this->time = $time;
    }
    /**
     * Return timings log
     *
     * @param bool $string Return array or formatted string
     *
     * @return array|string
     */
    public function getTime($string = true)
    {
        $this->timings[] = array(
            'time' => number_format(round(microtime(true) - $this->start, 7), 7),
            'message' => '<b>Total time</b>',
        );
        $this->timings[] = array(
            'time' => number_format(round((memory_get_usage(true)), 2), 0, ',', ' '),
            'message' => '<b>Memory usage</b>',
        );

        if (!$string) {
            return $this->timings;
        } else {
            $res = '';
            foreach ($this->timings as $v) {
                $res .= $v['time'] . ': ' . $v['message'] . "\n";
            }

            return $res;
        }
    }
    public function route($rule, $uri, $method, $request, $id){
        
        $resp = $this->checkPermissions($rule);
        
        if(!$resp['success']){
            header('HTTP/1.1 401 Unauthorized2');
            return $resp;
        }
        $req = json_decode(file_get_contents('php://input'), true);
        if(is_array($req)) $request = array_merge($request,$req);
        if(empty($request['sheet'])) return $this->error("empty sheet",['request'=>$request,'post'=>$req]);
        $sheet = json_decode($request['sheet'],1);
        $otchet = json_decode($request['otchet'],1);
        $resp = $this->import($sheet, $otchet);
        return $resp;

        return $this->success("Расчет импортирован",['request'=>$request]);
        
    }
    public function import($sheet, $otchet){
        $raschet = [
            'name'=>$sheet[6][$this->letters_to_num('L')], //L7
            //'index'=>$worksheet->getCell('A1')->getFormattedValue(),
            // 'date'=>$date,
            // 'org_id'=>$org_id,
            // 'created_by'=>,
            'discount'=>$sheet[3][$this->letters_to_num('AN')], //AN4
            'cost'=>$sheet[6][$this->letters_to_num('BW')], //BW7
            'status_id'=>1,
            'last'=>1,
            // 'comment'=>$worksheet->getCell('A1')->getFormattedValue(),
            // 'fix_discount'=>1,
        ];
        $kontragent = $sheet[5][$this->letters_to_num('L')];// $worksheet->getCell('L6')->getFormattedValue();
        //$this->addTime("kontragent $kontragent");
        if($kontragent){
            if($Orgs = $this->modx->getObject('Orgs',['shortname'=>$kontragent])){
                $raschet['org_id'] = $Orgs->id;
                if($Orgs->discount != $raschet['discount']) $raschet['fix_discount'] = 1;
            }
        }
        $c = $this->modx->newQuery('gsRaschet');
        $c->select('max(family_id) as cnt');
        if ($c->prepare() && $c->stmt->execute()) {
            $max_family_id = $c->stmt->fetchColumn();
            $raschet['family_id'] = $max_family_id + 1;
        }
        $date = $sheet[3][$this->letters_to_num('L')];//$worksheet->getCell('L4')->getFormattedValue();
        $raschet['date0'] = $date;
        if($date){
            $raschet['date'] = date('Y-m-d',strtotime($date));
        }
        $manager = $sheet[0][$this->letters_to_num('E')]; // $worksheet->getCell('E1')->getFormattedValue();
        //$this->addTime("manager $manager");
        if($manager){
            if($modUserProfile = $this->modx->getObject('modUserProfile',['fullname'=>$manager])){
                $raschet['created_by'] = $modUserProfile->internalKey;
            }
        }
        
        $row_itogo = false;
        $num_D = $this->letters_to_num('D');
        for($i = 14;$i<3000;$i++){
            if($sheet[$i][$num_D] === 'итого'){
                $row_itogo = $i;
                break;
            }
        }
        if(!$row_itogo) return $this->gtsShop->error("Не найдено итого");
        $this->addTime("row_itogo $row_itogo");
        $dets = $this->getDets($sheet,$row_itogo, $otchet);
        if($gsRaschet = $this->modx->newObject('gsRaschet',$raschet)){
            if($gsRaschet->save()){
                foreach($dets as $det){
                    $det['raschet_id'] = $gsRaschet->id;
                    if($gsRaschetProduct = $this->modx->newObject('gsRaschetProduct',$det)){
                        $gsRaschetProduct->save();
                    }
                }
                $resp = $this->gtsShop->pereraschet(['raschet_id'=>$gsRaschet->id]);
                //if(!$resp['success']) return $resp;
                $this->pdo->setConfig([
                    'class'=>'gsRaschetProduct',
                    'where'=>[
                        'raschet_id'=>$gsRaschet->id,
                    ],
                    'return'=>'data',
                    'limit'=>0,
                ]);
                $dets2 = $this->pdo->run();
                if($gsRaschet2 = $this->modx->getObject('gsRaschet',$gsRaschet->id)){
                    $raschet2 = $gsRaschet2->toArray();
                    return $this->success('Импортировано',[
                        'raschet'=>$raschet,
                        'dets'=>$dets,
                        'raschet2'=>$raschet2,
                        'dets2'=>$dets2,
                        'log'=>$this->getTime()
                    ]);
                }
            }
        }
        return $this->gtsShop->error("Ошибка! конец");
    }
    public function getDets($sheet, $row_itogo, $otchet) {
        $dets = [];
        $num_B = $this->letters_to_num('B');
        $num_D = $this->letters_to_num('D');
        $num_I = $this->letters_to_num('I');
        $num_BU = $this->letters_to_num('BU');
        $num_BZ = $this->letters_to_num('BZ');

        for($i = 14;$i<$row_itogo;$i++){
            $det = []; $det_excel = [];
            if($sheet[$i][$num_D]){
                $det['sech_id1'] = 1;
                $det['excel_name1'] = $sheet[$i][$num_D];
                $det['count'] = $sheet[$i][$num_I];
            }else if($sheet[$i][$num_BU]){
                $det['sech_id1'] = 2;
                $det['excel_name1'] = $sheet[$i][$num_BU];
                $det['count'] = $sheet[$i][$num_BZ];
            }
            if(isset($det['sech_id1'])){
                $det_excel = [];
                $det['mark'] = $otchet[$i-8][$num_B];
                $mark = array_map('trim', explode('/', $det['mark']));
                if(count($mark) == 2){
                    $det['detail_nom_id'] = (int)$mark[1];
                }else{
                    $det['detail_nom_id'] = (int)$mark[0];
                }
                $det['excel_row'] = $i;

                for($j = 1;$j<=175;$j++){
                    $colname = $this->num_to_letters($j);
                    $det_excel[$colname] = $sheet[$i][$j-1];//$worksheet->getCell($colname.$i)->getFormattedValue();
                }
                if($gsProductData = $this->modx->getObject('gsProductData',[
                    'excel_name'=>$det['excel_name1'],
                    'sech_id'=>$det['sech_id1'],
                    'available_for_manager'=>1,
                ])){
                    $det['product_id'] = $gsProductData->id;
                }else{
                    $det['product_id'] = 491;
                    $det['name'] = $det['excel_name1'];
                }
                $this->pdo->setConfig([
                    'class'=>'gsProductParam',
                    'leftJoin'=>[
                        'gsParam'=>[
                            'class'=>'gsParam',
                            'on'=>'gsParam.id=gsProductParam.param_id',
                        ]
                    ],
                    'where'=>[
                        'gsProductParam.product_id'=>$det['product_id'],
                        'gsProductParam.active'=>1,
                    ],
                    'select'=>[
                        'gsProductParam'=>'*',
                        'gsParam'=>'gsParam.title,gsParam.name,gsParam.excel_pryam,gsParam.excel_krug',
                    ],
                    'sortby'=>[
                        'gsProductParam.default_formula_sort'=>'ASC',
                    ],
                    'limit'=>0,
                    'return'=>'data',
                ]);
                $gsProductParams = $this->pdo->run();
                foreach($gsProductParams as $gsProductParam){
                    $formula = $gsProductParam['formula_excel'];
                    // $det[$gsProductParam['name'].'formula'] .= "$formula";
                    if(empty($formula)){
                        if($det['sech_id1'] == 1){
                            $formula = $gsProductParam['excel_pryam'];
                        }else{
                            $formula = $gsProductParam['excel_krug'];
                        }
                    }
                    if(empty($formula)) continue;
                    // $det[$gsProductParam['name'].'formula'] .= "$formula";
                    $formula = $this->generate_formula($formula, $det_excel);
                    $php_formulas = [
                        'find_material',
                    ];
                    foreach($php_formulas as $php_formula){
                        if(strpos($formula, $php_formula) !== false){
                            //$this->addTime("formula $formula");
                            $formula = $this->run_formula($det, $formula, $php_formula);
                        }
                    }
                    try{
                        $det[$gsProductParam['name']] = $this->getTables->calc_excel_formula("=".$formula);
                        // $det[$gsProductParam['name'].'error'] = "Ошибка в формуле $formula для детали!";
                    }catch(Exception $e){
                        $det[$gsProductParam['name'].'error'] = "Ошибка в формуле $formula для детали!";
                    }
                }
                if($det['comment']){
                    $comment = mb_convert_case($det['comment'], MB_CASE_LOWER, "UTF-8");
                    $pos = strpos($comment, "тип");
                    if ($pos !== false) {
                        $det['detType'] = substr($comment, $pos, 8);
                    }
                }
                $dets[] = $det;
            }
        }
        return $dets;
    }
    public function run_formula($det, $formula, $php_formula)
    {
        $pos = strpos($formula, $php_formula);
        $pos2 = strpos($formula, '(',$pos); 
        $pos3 = strpos($formula, ')',$pos2); 
        $params = substr($formula, $pos2 + 1, $pos3 - $pos2 - 1);
        $f = substr($formula, $pos, $pos3 - $pos + 1);
        $params = array_map('trim', explode(';', $params));
        $value = $this->{$php_formula}($det, $params);
        $formula = str_replace($f,$value,$formula);
        return $formula;
    }
    public function find_material($det, $params)
    {
        if($gsProductData = $this->modx->getObject('gsProductData',$det['product_id'])){
            $excel_mat_name = $gsProductData->excel_mat_name;
        }
        
        if(empty($excel_mat_name)) $excel_mat_name = 'лист';
        //$this->addTime("excel_mat_name $excel_mat_name ".str_replace(',','.',$params[0])." ".$params[1]);
        if($mat = $this->modx->getObject("gsMaterial",[
            'name:LIKE'=>$excel_mat_name,
            'code'=>str_replace(',','.',$params[0]),
            'type'=>$params[1],
        ])){
			return $mat->id;
		}
        return 0;
    }
    public function generate_formula($formula, $data)
    {
        foreach($data as $k=>$v){
            $formula = str_replace("'".$k."'",$v, $formula);
        }
        return $formula;
    }
    public function num_to_letters($num, $uppercase = true) {
        $letters = '';
        while ($num > 0) {
            $code = ($num % 26 == 0) ? 26 : $num % 26;
            $letters .= chr($code + 64);
            $num = ($num - $code) / 26;
        }
        return ($uppercase) ? strtoupper(strrev($letters)) : strrev($letters);
    }
    public function letters_to_num($letters) {
        $num = 0;
        $arr = array_reverse(str_split($letters));
    
        for ($i = 0; $i < count($arr); $i++) {
            $num += (ord(strtolower($arr[$i])) - 96) * (pow(26,$i));
        }
        return $num - 1;
    }
    public function success($message = "",$data = []){
        return array('success'=>1,'message'=>$message,'data'=>$data);
    }
    public function error($message = "",$data = []){
        return array('success'=>0,'message'=>$message,'data'=>$data);
    }
    public function checkPermissions($rule_action){
        if($rule_action['authenticated']){
            if(!$this->modx->user->id > 0) return $this->error("Not api authenticated!",['user_id'=>$this->modx->user->id]);
        }
        if($rule_action['groups']){
            $groups = array_map('trim', explode(',', $rule_action['groups']));
            if(!$this->modx->user->isMember($groups)) return $this->error("Not api permission groups!");
        }
        if($rule_action['permitions']){
            $permitions = array_map('trim', explode(',', $rule_action['permitions']));
            foreach($permitions as $pm){
                if(!$this->modx->hasPermission($pm)) return $this->error("Not api modx permission!");
            }
        }
        return $this->success();
    }
}