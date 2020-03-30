<?php
/**
 * The identity
 *
 * Author: Lin07ux
 * Created_at: 2018-12-13 23:09:08
 */

namespace AntOpen\Identity;

abstract class Identity
{
    /**
     * 用户类别：个人
     */
    const IDENTITY_TYPE_PERSONAL = 'CERT_INFO';

    /**
     * 用户类别：企业
     */
    const IDENTITY_TYPE_ENTERPRISE = 'EP_CERT_INFO';

    /**
     * 证件类型：身份证(个人)
     */
    const CERT_TYPE_PERSONAL = 'IDENTITY_CARD';

    /**
     * 证件类型：统一社会信用代码(企业)
     */
    const CERT_TYPE_EP_MERGE = 'NATIONAL_LEGAL_MERGE';

    /**
     * 证件类型：企业工商注册号(企业)
     */
    const CERT_TYPE_EP_REGISTER = 'NATIONAL_LEGAL';

    /**
     * @var array 身份信息
     */
    protected $data = [];

    /**
     * 设置身份信息
     *
     * @param  array $data
     * @return $this
     */
    abstract public function setData (array $data);

    /**
     * 获取全部身份信息
     *
     * @return array
     */
    abstract public function toArray ();
}