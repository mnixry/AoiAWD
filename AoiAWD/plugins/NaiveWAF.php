<?php

namespace aoiawd\plugin;

use aoiawd\DBHelper;

new NaiveWAF(PluginManager::getInstance());

const COLLECTION_NAME = 'waf_rule';

const WAF_TRIGGERED_DEFAULT_RESPONSE = '
The quick brown fox jumps over the lazy dog.
The rain in Spain falls mainly on the plain.

Please don\'t attack me.

flag{waf_is_not_a_firewall}';

class NaiveWAF
{
    private  $pluginManager;

    public function __construct(PluginManager $manager)
    {
        $this->pluginManager = $manager;
        $this->pluginManager->register('Web', 'processLog', [$this, 'processWebBuffer']);
    }

    public function processWebBuffer($data)
    {
        $coll = DBHelper::getDB()->selectCollection(COLLECTION_NAME);
        $rules = $coll->find();
        $matched_rule = null;
        foreach ($rules as $rule) {
            $match_expression = $rule['expression'];
            $rule_name = $rule['name'] ?? $rule['_id'];
            $matched = false;

            switch ($match_type = $match_expression['type']) {
                case 'regex':
                    $field = $match_expression['field'];
                    $regex = $match_expression['regex'];
                    $matched = !!preg_match($regex, $data['data'][$field]);
                    break;
                case 'function':
                    $expression = $match_expression['code'];
                    try {
                        $matched = !!eval("return {$expression};");
                    } catch (\Throwable $e) {
                        $this->pluginManager->getInvoker()->setAlert('NaiveWAF', "规则{$rule_name}匹配失败，表达式错误: {$e->getMessage()}");
                    }
                    break;
                default:
                    throw new \Exception("Unknown match expression type: {$match_type}");
                    break;
            }

            if ($matched) {
                $matched_rule = $rule;
                break;
            }
        }

        if ($matched_rule) {
            $rule_name = $matched_rule['name'] ?? $matched_rule['_id'];
            $this->pluginManager->getInvoker()->setAlert('NaiveWAF', "规则{$rule_name}匹配成功，已阻止请求");
            $response_rule = $matched_rule['response'] ?? [
                'type' => 'text',
                'content' => WAF_TRIGGERED_DEFAULT_RESPONSE
            ];
            $return_buffer = &$data['buffer'];
            switch ($response_type = $response_rule['type']) {
                case 'text':
                    $return_buffer = $response_rule['content'];
                    break;
                case 'replace':
                    $return_buffer = preg_replace($response_rule['regex'], $response_rule['replacement'], $return_buffer);
                    break;
                default:
                    throw new \Exception("Unknown response type: {$response_type}");
                    break;
            }
        }

        return $data;
    }
}
