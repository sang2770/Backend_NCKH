<?php

namespace App\Policies;

use App\Models\Tb_tk_quanly;
use App\Models\Tb_tk_sinhvien;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Tb_tk_sinhvien $tbTkSinhvien)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @param  \App\Models\Tb_tk_quanly  $tbTkQuanly
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Tb_tk_sinhvien $tbTkSinhvien, Tb_tk_quanly $tbTkQuanly)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Tb_tk_sinhvien $tbTkSinhvien)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @param  \App\Models\Tb_tk_quanly  $tbTkQuanly
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Tb_tk_sinhvien $tbTkSinhvien, Tb_tk_quanly $tbTkQuanly)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @param  \App\Models\Tb_tk_quanly  $tbTkQuanly
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Tb_tk_sinhvien $tbTkSinhvien, Tb_tk_quanly $tbTkQuanly)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @param  \App\Models\Tb_tk_quanly  $tbTkQuanly
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Tb_tk_sinhvien $tbTkSinhvien, Tb_tk_quanly $tbTkQuanly)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Tb_tk_sinhvien  $tbTkSinhvien
     * @param  \App\Models\Tb_tk_quanly  $tbTkQuanly
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Tb_tk_sinhvien $tbTkSinhvien, Tb_tk_quanly $tbTkQuanly)
    {
        //
    }
}
