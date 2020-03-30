<?php
/**
 * The identity for Enterprise
 *
 * Author: Lin07ux
 * Created_at: 2018-12-13 23:49:41
 */

namespace AntOpen\Identity;

use AntOpen\Exception\InvalidIdentityPropertyValueException;

class Enterprise extends Identity
{
    public function __construct ($name, $cert, $epName, $epCert, $type = self::CERT_TYPE_EP_MERGE)
    {
        $this->setData([
            'ep_cert_type' => $type,
            'ep_cert_name' => $epName,
            'ep_cert_no' => $epCert,
            'cert_name' => $name,
            'cert_no' => $cert,
        ]);
    }

    /**
     * 设置身份信息
     *
     * @param  array  $data
     * @return $this
     */
    public function setData (array $data)
    {
        $this->data = [
            'identity_type' => self::IDENTITY_TYPE_ENTERPRISE,
            'cert_type' => self::CERT_TYPE_PERSONAL,
            'cert_name' => ! empty($data['cert_name']) ? $data['cert_name'] : null,
            'cert_no' => ! empty($data['cert_no']) ? $data['cert_no'] : null,
            'ep_cert_type' => ! empty($data['ep_cert_type']) ? $data['ep_cert_type'] : null,
            'ep_cert_name' => ! empty($data['ep_cert_name']) ? $data['ep_cert_name'] : null,
            'ep_cert_no' => ! empty($data['ep_cert_no']) ? $data['ep_cert_no'] : null,
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
            throw new InvalidIdentityPropertyValueException('The cert name of enterprise legal person (cert_name) can not be empty');
        }

        if (empty($this->data['cert_no'])) {
            throw new InvalidIdentityPropertyValueException('The cert no of enterprise legal person (cert_no) can not be empty');
        }

        if (! preg_match('/^\d{15}(\d{2}[\dxX])?$/', $this->data['cert_no'])) {
            throw new \InvalidArgumentException(
                'The value of cert no of enterprise legal person should be the correct ID number of mainland China'
            );
        }

        if (empty($this->data['ep_cert_name'])) {
            throw new InvalidIdentityPropertyValueException('The name of enterprise (ep_cert_name) can not be empty');
        }

        if (empty($this->data['ep_cert_no'])) {
            throw new InvalidIdentityPropertyValueException('The cert no of enterprise (ep_cert_no) can not be empty');
        }

        if ($this->data['ep_cert_type'] === self::CERT_TYPE_EP_MERGE) {
            if (! preg_match('/^[^_IOZSVa-z\W]{2}\d{6}[^_IOZSVa-z\W]{10}$/', $this->data['ep_cert_no'])) {
                throw new InvalidIdentityPropertyValueException(
                    'The cert no of enterprise is not a valid national unified social credit code'
                );
            }
        } elseif ($this->data['ep_cert_type'] === self::CERT_TYPE_EP_REGISTER) {
            if (! preg_match('/^[A-Za-z0-9]\w{14}$/', $this->data['ep_cert_no'])) {
                throw new InvalidIdentityPropertyValueException(
                    'The cert no of enterprise is not a valid national business registration number'
                );
            }
        } else {
            throw new InvalidIdentityPropertyValueException('The cert type of enterprise (ep_cert_type) is invalid');
        }

        return $this->data;
    }
}