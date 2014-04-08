<?php

/**
 * 生成随机唯一id
 * @author wuheping(heping.wu@chukong-inc.com)
 * @date 2013-8-12
 */
class GuidHelper {

        var $valueBeforeMD5;
        var $valueAfterMD5;

        public function getUUID() {
                $address = UUIDNetAddress::getLocalHost();
                $this->valueBeforeMD5 = $address->toString() . ':' . UUIDSystem::currentTimeMillis() . ':' . UUIDRandom::nextLong();
                $this->valueAfterMD5 = md5($this->valueBeforeMD5);
                $raw = strtoupper($this->valueAfterMD5);
                return strtoupper(substr($raw, 0, 8) . '-' . substr($raw, 8, 4) . '-' . substr($raw, 12, 4) . '-' . substr($raw, 16, 4) . '-' . substr($raw, 20));
                //return $this->toString();
        }

        public function getUUIDSecret($uuid) {
                return md5($uuid);
        }

        public function toString() {
                $raw = strtoupper($this->valueAfterMD5);
                return strtoupper(substr($raw, 0, 8) . '-' . substr($raw, 8, 4) . '-' . substr($raw, 12, 4) . '-' . substr($raw, 16, 4) . '-' . substr($raw, 20));
        }

}

class UUIDSystem {

        function currentTimeMillis() {
                list($usec, $sec) = explode(" ", microtime());
                return $sec . substr($usec, 2, 3);
        }

}

class UUIDNetAddress {

        var $Name = 'localhost';
        var $IP = '127.0.0.1';

        function getLocalHost() { // static
                $address = new UUIDNetAddress();
                if (isset($_SERVER["SERVER_NAME"])) {
                        $address->Name = $_SERVER["SERVER_NAME"] . rand(0, 65535);
                }

                if (isset($_SERVER['SERVER_ADDR']))
                        $address->IP = $_SERVER["SERVER_ADDR"];
                if (null == $address->IP || "" == $address->IP)
                        $address->IP = "127.0.0.1";
                return $address;
        }

        function toString() {
                return strtolower($this->Name . '/' . $this->IP);
        }

}

class UUIDRandom {

        function nextLong() {
                $tmp = rand(0, 1) ? '-' : '';
                return $tmp . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999) . rand(100, 999) . rand(100, 999);
        }

}
