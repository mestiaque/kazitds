@extends('me::master')

@section('title', 'Shopkeeper ব্যবহারকারী নির্দেশিকা')

@push('css')
<style>
.card-header {
    background-color: #f8f9fa;
    cursor: pointer;
}
.point-list li {
    margin-bottom: 0.5rem;
}
.point-title {
    font-weight: bold;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="accordion" id="manualAccordion">

        {{-- ১. ড্যাশবোর্ড --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingDashboard">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDashboard" aria-expanded="false" aria-controls="collapseDashboard">
                    ১. ড্যাশবোর্ড
                </button>
            </h2>
            <div id="collapseDashboard" class="accordion-collapse collapse" aria-labelledby="headingDashboard" data-bs-parent="#manualAccordion">
                <div class="accordion-body">

                    {{-- মূল তথ্য --}}
                    <div class="card mb-2">
                        <div class="card-header">১.১ মূল তথ্য</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">মোট পণ্য:</span> সিস্টেমে যোগ হওয়া সব পণ্যের সংখ্যা।</li>
                            <li><span class="point-title">আজকের বিক্রয়:</span> আজকের মোট বিক্রয়ের টাকা।</li>
                            <li><span class="point-title">আজকের ক্রয়:</span> আজকের মোট ক্রয়ের টাকা।</li>
                            <li><span class="point-title">কম স্টক পণ্য:</span> যেসব পণ্যের স্টক কম আছে, তাদের সংখ্যা।</li>
                        </div>
                    </div>

                    {{-- বিক্রয় চিত্র --}}
                    <div class="card mb-2">
                        <div class="card-header">১.২ বিক্রয় চিত্র</div>
                        <div class="card-body point-list">
                            <li>দৈনিক/সাপ্তাহিক বিক্রয় চিত্র আকারে প্রদর্শিত হবে।</li>
                            <li>X-অক্ষ: তারিখ, Y-অক্ষ: বিক্রয় (টাকা)</li>
                            <li>গ্রাফে মাউস রাখলে নির্দিষ্ট দিনের বিক্রয় দেখা যাবে।</li>
                        </div>
                    </div>

                    {{-- সর্বাধিক বিক্রিত পণ্য --}}
                    <div class="card mb-2">
                        <div class="card-header">১.৩ সর্বাধিক বিক্রিত পণ্য</div>
                        <div class="card-body point-list">
                            <li>সর্বাধিক বিক্রিত ৫টি পণ্যের তালিকা।</li>
                            <li>পরিমাণ অনুযায়ী প্রদর্শিত হবে।</li>
                            <li>তথ্য সর্বশেষ বিক্রয়ের ভিত্তিতে আপডেট হয়।</li>
                        </div>
                    </div>

                    {{-- দ্রুত অ্যাক্সেস --}}
                    <div class="card mb-2">
                        <div class="card-header">১.৪ দ্রুত অ্যাক্সেস</div>
                        <div class="card-body point-list">
                            <li>ড্যাশবোর্ড থেকে সরাসরি পণ্য, ব্র্যান্ড, ক্রয়, বিক্রয়, প্যাক, ভেরিয়েন্ট, স্টক, সেটিংস ইত্যাদিতে যাওয়া যাবে।</li>
                            <li>প্রতিটি দ্রুত অ্যাক্সেস বাটনে আইকন ও লেবেল আছে।</li>
                        </div>
                    </div>

                    {{-- সাম্প্রতিক কার্যক্রম --}}
                    <div class="card mb-2">
                        <div class="card-header">১.৫ সাম্প্রতিক কার্যক্রম</div>
                        <div class="card-body point-list">
                            <li>সাম্প্রতিক বিক্রয়: সর্বশেষ ৫টি বিক্রয়, ইনভয়েস, তারিখ, গ্রাহক, টাকা দেখাবে।</li>
                            <li>সাম্প্রতিক ক্রয়: সর্বশেষ ৫টি ক্রয়, ক্রয় নং, তারিখ, পণ্য, টাকা দেখাবে।</li>
                            <li>সব লিস্টে লিঙ্ক থাকবে, ক্লিক করলে বিস্তারিত দেখা যাবে।</li>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- ২. মাস্টার ডেটা --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingMasterData">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMasterData" aria-expanded="false" aria-controls="collapseMasterData">
                    ২. মাস্টার ডেটা
                </button>
            </h2>
            <div id="collapseMasterData" class="accordion-collapse collapse" aria-labelledby="headingMasterData" data-bs-parent="#manualAccordion">
                <div class="accordion-body">

                    {{-- পণ্য --}}
                    <div class="card mb-2">
                        <div class="card-header">২.১ পণ্য</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">নতুন পণ্য:</span> নাম যোগ করা যাবে।</li>
                            <li><span class="point-title">উদাহরণ:</span> আঠাস চাল, মিনিকেট চাল, চিনিগুড়া চাল, সরিষা তেল, সয়াবিন তেল ইত্যাদি</li>
                            <li><span class="point-title">পণ্য তালিকা:</span> সব পণ্য দেখা যাবে। নাম দিয়ে খুঁজতে পারবে। পেজ অনুযায়ী ভাগ হবে।</li>
                            <li><span class="point-title">পণ্য সম্পাদনা:</span> নাম পরিবর্তন করা যাবে।</li>
                            <li><span class="point-title">পণ্য মুছে ফেলা:</span> কোনো ভেরিয়েন্ট না থাকলে পণ্য মুছে ফেলা যাবে।</li>
                        </div>
                    </div>

                    {{-- ব্র্যান্ড --}}
                    <div class="card mb-2">
                        <div class="card-header">২.২ ব্র্যান্ড</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">নতুন ব্র্যান্ড:</span> ব্র্যান্ডের নাম লিখে তৈরি করা যাবে।</li>
                            <li><span class="point-title">উদাহরণ:</span> প্রাণ, তীর, নিজস্ব ইত্যাদি</li>
                            <li><span class="point-title">ব্র্যান্ড তালিকা:</span> সব ব্র্যান্ড দেখা যাবে। নাম দিয়ে খুঁজতে পারবে।</li>
                            <li><span class="point-title">ব্র্যান্ড সম্পাদনা:</span> নাম পরিবর্তন করা যাবে।</li>
                            <li><span class="point-title">ব্র্যান্ড মুছে ফেলা:</span> যদি ভেরিয়েন্ট যুক্ত থাকে তবে মুছে ফেলা যাবে না।</li>
                        </div>
                    </div>

                    {{-- প্যাক --}}
                    <div class="card mb-2">
                        <div class="card-header">২.৩ প্যাক</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">নতুন প্যাক:</span> নাম লিখে তৈরি করা যাবে।</li>
                            <li><span class="point-title">উদাহরণ:</span> ১ কেজি, ১০ কেজি, ১ লিটার, ৫ লিটার ইত্যাদি</li>
                            <li><span class="point-title">প্যাক তালিকা:</span> সব প্যাক দেখা যাবে। নাম দিয়ে খুঁজতে পারবে।</li>
                            <li><span class="point-title">প্যাক সম্পাদনা:</span> নাম পরিবর্তন করা যাবে।</li>
                            <li><span class="point-title">প্যাক মুছে ফেলা:</span> যদি ভেরিয়েন্ট যুক্ত থাকে তবে মুছে ফেলা যাবে না।</li>
                        </div>
                    </div>

                    {{-- প্রোডাক্ট ভেরিয়েন্ট --}}
                    <div class="card mb-2">
                        <div class="card-header">২.৪ ভেরিয়েন্ট</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">নতুন ভেরিয়েন্ট:</span> পণ্য, ব্র্যান্ড (ঐচ্ছিক) ও প্যাক নির্বাচন করে তৈরি করা যাবে।</li>
                            <li><span class="point-title">উদাহরণ:</span> পণ্য: মিনিকেট চাল, ব্র্যান্ড: নিজস্ব, প্যাক: ১০ কেজি</li>
                            <li><span class="point-title">ভেরিয়েন্ট তালিকা:</span> সব ভেরিয়েন্ট দেখা যাবে।</li>
                            <li><span class="point-title">ভেরিয়েন্ট সম্পাদনা:</span> পরিবর্তন করা যাবে।</li>
                            <li><span class="point-title">ভেরিয়েন্ট মুছে ফেলা:</span> যদি ক্রয় যুক্ত থাকে তবে মুছে ফেলা যাবে না।</li>
                        </div>
                    </div>

                    {{-- সরবরাহকারী --}}
                    <div class="card mb-2">
                        <div class="card-header">২.৫ সরবরাহকারী</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">নতুন সরবরাহকারী:</span> নাম, কোম্পানি নাম, ফোন, ইমেইল, ঠিকানা ইত্যাদি।</li>
                            <li><span class="point-title">সরবরাহকারী তালিকা:</span> নাম বা ফোন দিয়ে খুঁজতে পারবে।</li>
                            <li><span class="point-title">সরবরাহকারী সম্পাদনা:</span> সব তথ্য পরিবর্তন করা যাবে।</li>
                            <li><span class="point-title">সরবরাহকারী মুছে ফেলা:</span> যদি ক্রয় যুক্ত থাকে তবে মুছে ফেলা যাবে না।</li>
                        </div>
                    </div>

                    {{-- গ্রাহক --}}
                    <div class="card mb-2">
                        <div class="card-header">২.৬ গ্রাহক</div>
                        <div class="card-body point-list">
                            <li><span class="point-title">নতুন গ্রাহক:</span> নাম, ফোন, ইমেইল, ঠিকানা, পাওনা।</li>
                            <li><span class="point-title">গ্রাহক তালিকা:</span> নাম বা ফোন দিয়ে খুঁজতে পারবে।</li>
                            <li><span class="point-title">গ্রাহক সম্পাদনা:</span> সব তথ্য পরিবর্তন করা যাবে।</li>
                            <li><span class="point-title">গ্রাহক মুছে ফেলা:</span> যদি বিক্রয় যুক্ত থাকে তবে মুছে ফেলা যাবে না।</li>
                            <li><span class="point-title text-danger">বিঃ দ্রঃ :</span> গ্রাহকের ফোন নম্বর ইংরেজিতে দিতে হবে।</li>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- ৩. লেনদেন --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTransactions">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransactions" aria-expanded="false" aria-controls="collapseTransactions">
                    ৩. লেনদেন
                </button>
            </h2>
            <div id="collapseTransactions" class="accordion-collapse collapse" aria-labelledby="headingTransactions" data-bs-parent="#manualAccordion">
                <div class="accordion-body">

                    {{-- ক্রয় --}}
                    <div class="card mb-3">
                        <div class="card-header fw-bold">৩.১ ক্রয়</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><b>নতুন ক্রয়:</b> সরবরাহকারী নির্বাচন করে পণ্য ও দাম দিতে হবে।</li>
                            <li class="list-group-item"><b>ক্রয় তালিকা:</b> সব ক্রয় দেখা যাবে।</li>
                            <li class="list-group-item"><b>ক্রয় বিস্তারিত:</b> সব তথ্য দেখা যাবে।</li>
                            <li class="list-group-item"><b>ক্রয় সম্পাদনা:</b> পণ্য, দাম, তারিখ পরিবর্তন করা যাবে।</li>
                            <li class="list-group-item"><b>ক্রয় মুছে ফেলা:</b> স্টকে পর্যাপ্ত পণ্য থাকলে মুছে ফেলা যাবে।</li>
                        </ul>
                    </div>

                    {{-- বিক্রয় --}}
                    <div class="card mb-3">
                        <div class="card-header fw-bold">৩.২ বিক্রয়</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><b>নতুন বিক্রয়:</b> গ্রাহক নির্বাচন করে বিক্রয় তৈরি করা যাবে।</li>
                            <li class="list-group-item"><b>বিক্রয় তালিকা:</b> সব বিক্রয় দেখা যাবে।</li>
                            <li class="list-group-item"><b>বিক্রয় বিস্তারিত:</b> বিক্রয়ের সব তথ্য দেখা যাবে।</li>
                            <li class="list-group-item"><b>বিক্রয় সম্পাদনা:</b> শুধুমাত্র সর্বশেষ বিক্রয় সম্পাদনা করা যাবে।</li>
                            <li class="list-group-item"><b>বিক্রয় মুছে ফেলা:</b> শুধুমাত্র সর্বশেষ বিক্রয় মুছে ফেলা যাবে।</li>
                        </ul>
                    </div>

                    {{-- পাওনা সংগ্রহ --}}
                    <div class="card mb-3">
                        <div class="card-header fw-bold">৩.৩ পাওনা সংগ্রহ</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><b>ডিউ তালিকা:</b> যেসব গ্রাহকের টাকা বাকি আছে দেখা যাবে।</li>
                            <li class="list-group-item"><b>পেমেন্ট:</b> নগদ, বিকাশ, নগদ, রকেট, ব্যাংক ইত্যাদি।</li>
                            <li class="list-group-item"><b>পেমেন্ট হিস্টোরি:</b> আগের সব পেমেন্ট দেখা যাবে।</li>
                            <li class="list-group-item"><b>ডিউ রিমাইন্ডার:</b> SMS পাঠানো যাবে।</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>


        {{-- ৪. রিপোর্ট --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingReports">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
                    ৪. রিপোর্ট
                </button>
            </h2>
            <div id="collapseReports" class="accordion-collapse collapse" aria-labelledby="headingReports" data-bs-parent="#manualAccordion">
                <div class="accordion-body">

                    {{-- স্টক রিপোর্ট --}}
                    <div class="card mb-2">
                        <div class="card-header">৪.১ স্টক রিপোর্ট</div>
                        <div class="card-body point-list">
                            <li>সব ভেরিয়েন্টের স্টক দেখা যাবে।</li>
                            <li>ফিল্টার করা যাবে।</li>
                        </div>
                    </div>

                    {{-- বিক্রয় রিপোর্ট --}}
                    <div class="card mb-2">
                        <div class="card-header">৪.২ বিক্রয় রিপোর্ট</div>
                        <div class="card-body point-list">
                            <li>সব বিক্রয় দেখা যাবে।</li>
                            <li>ফিল্টার করা যাবে।</li>
                        </div>
                    </div>

                    {{-- ক্রয় রিপোর্ট --}}
                    <div class="card mb-2">
                        <div class="card-header">৪.৩ ক্রয় রিপোর্ট</div>
                        <div class="card-body point-list">
                            <li>সব ক্রয় দেখা যাবে।</li>
                        </div>
                    </div>

                    {{-- কম স্টক রিপোর্ট --}}
                    <div class="card mb-2">
                        <div class="card-header">৪.৪ কম স্টক রিপোর্ট</div>
                        <div class="card-body point-list">
                            <li>কম স্টক আইটেম দেখা যাবে।</li>
                        </div>
                    </div>

                    {{-- এসএমএস রিপোর্ট --}}
                    <div class="card mb-2">
                        <div class="card-header">৪.৫ এসএমএস রিপোর্ট</div>
                        <div class="card-body point-list">
                            <li>পাঠানো SMS এর তথ্য দেখা যাবে।</li>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- ৫. দোকানের সেটিংস --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingSettings">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">
                    ৫. দোকানের সেটিংস
                </button>
            </h2>
            <div id="collapseSettings" class="accordion-collapse collapse" aria-labelledby="headingSettings" data-bs-parent="#manualAccordion">
                <div class="accordion-body">

                    {{-- সাধারণ সেটিংস --}}
                    <div class="card mb-2">
                        <div class="card-header">৫.১ সাধারণ সেটিংস</div>
                        <div class="card-body point-list">
                            <li>দোকানের নাম, ঠিকানা, ফোন, ইমেইল সেট করা যাবে।</li>
                        </div>
                    </div>

                    {{-- কম স্টক সতর্কতা --}}
                    <div class="card mb-2">
                        <div class="card-header">৫.২ কম স্টক সতর্কতা</div>
                        <div class="card-body point-list">
                            <li>স্টক থ্রেশহোল্ড সেট করা যাবে।</li>
                        </div>
                    </div>

                    {{-- SMS নোটিফিকেশন --}}
                    @if(get_setting('enable_sms'))
                    <div class="card mb-2">
                        <div class="card-header">৫.৩ SMS নোটিফিকেশন</div>
                        <div class="card-body point-list">
                            <li>বিক্রয়, পেমেন্ট, রিমাইন্ডার SMS চালু/বন্ধ করা যাবে।</li>
                        </div>
                    </div>
                    @endif

                    {{-- দোকানের লোগো --}}
                    <div class="card mb-2">
                        <div class="card-header">৫.৪ দোকানের লোগো</div>
                        <div class="card-body point-list">
                            <li>লোগো আপলোড ও প্রিভিউ দেখা যাবে।</li>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div> <!-- /accordion -->

</div> <!-- /container -->
@endsection

@push('js')
<script>
    // Optional: Collapse all except first
    var manualAccordion = document.getElementById('manualAccordion');
    var bsCollapse = new bootstrap.Collapse(manualAccordion.querySelector('.collapse.show'), {toggle: false});
</script>
@endpush
