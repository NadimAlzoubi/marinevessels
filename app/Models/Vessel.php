<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vessel_name', // اسم السفينة
        'job_no',  // رقم الوظيفة
        'port_name',   // اسم الميناء
        'eta',         // وقت الوصول المتوقع
        'etd',         // وقت المغادرة المتوقع
        'status',      // الحالة
        'berth_no',    // رقم الرصيف
        'voy',          // رقم الرحلة
        'grt',          // الوزن الإجمالي
        'nrt',          // الوزن الصافي
        'dwt',          // الوزن القابل للشحن
        'eosp',         // وقت نهاية العملية
        'aado',         // وقت الوصول للواجهة
        'nor_tendered', // وقت تقديم إشعار الوصول
        'nor_accepted', // وقت قبول إشعار الوصول
        'dropped_anchor', // وقت إسقاط المرساة
        'heaved_up_anchor', // وقت رفع المرساة
        'pilot_boarded', // وقت صعود القائد
        'first_line',   // وقت بدء الرباط الأول
        'berthed_on',   // وقت الرسو
        'made_fast',    // وقت تثبيت السفينة
        'sailed_on',    // وقت الإبحار
        'arrival_fuel_oil', // كمية الوقود عند الوصول
        'arrival_diesel_oil', // كمية الديزل عند الوصول
        'arrival_fresh_water', // كمية المياه العذبة عند الوصول
        'arrival_draft_fwd', // غاطس السفينة الأمامي عند الوصول
        'arrival_draft_aft', // غاطس السفينة الخلفي عند الوصول
        'departure_fuel_oil', // كمية الوقود عند المغادرة
        'departure_diesel_oil', // كمية الديزل عند المغادرة
        'departure_fresh_water', // كمية المياه العذبة عند المغادرة
        'departure_draft_fwd', // غاطس السفينة الأمامي عند المغادرة
        'departure_draft_aft', // غاطس السفينة الخلفي عند المغادرة
        'next_port_of_call', // الميناء القادم
        'eta_next_port',  // وقت الوصول للميناء القادم
        'any_requirements', // أي متطلبات خاصة
        'client_id', // رمز العميل
    ];


    // السماح بتعبئة جميع الحقول
    // protected $guarded = [];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'eta' => 'datetime',
        'etd' => 'datetime',
        'eosp' => 'datetime',
        'aado' => 'datetime',
        'nor_tendered' => 'datetime',
        'nor_accepted' => 'datetime',
        'dropped_anchor' => 'datetime',
        'heaved_up_anchor' => 'datetime',
        'pilot_boarded' => 'datetime',
        'first_line' => 'datetime',
        'berthed_on' => 'datetime',
        'made_fast' => 'datetime',
        'sailed_on' => 'datetime',
        'eta_next_port' => 'datetime',
    ];


    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }   

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
