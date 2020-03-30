<?php
/**
 * The identity for Person
 *
 * Author: Lin07ux
 * Created_at: 2018-12-13 23:38:39
 */

namespace AntOpen\Identity;

use AntOpen\Exception\InvalidIdentityPropertyValueException;

class Person extends Identity
{
    /**
     * Person constructor.
     *
     * @param string $name 姓名
     * @param string $cert 身份证号
     */
    public function __construct ($name, $cert)
    {
        $this->setData(['cert_name' => $name, 'cert_no' => $cert]);
    }

    /**
     * 设置身份信息
     *
     * @param  array $data
     * @return $this
     */
    public function setData (array $data)
    {
        $this->data = [
            'identity_type' => self::IDENTITY_TYPE_PERSONAL,
            'cert_type' => self::CERT_TYPE_PERSONAL,
            'cert_name' => ! empty($data['cert_name']) ? $data['cert_name'] : null,
            'cert_no' => ! empty($data['cert_no']) ? $data['cert_no'] : null
        ];

        return $this;
    }

    /**
     * 获取全部身份信息
     *
     * @return array
     */
    public function toArray ()
    {
        if (empty($this->data['cert_name'])) {
            throw new InvalidIdentityPropertyValueException('The name of person can not be empty');
        }

        if (empty($this->data['cert_no'])) {
            throw new InvalidIdentityPropertyValueException('The cert no of person can not be empty');
        }

        if (! preg_match('/^\d{15}(\d{2}[\dxX])?$/', $this->data['cert_no'])) {
            throw new \InvalidArgumentException('The value of cert no of person should be the correct ID number of mainland China');
        }
        
        return $this->data;
    }
}