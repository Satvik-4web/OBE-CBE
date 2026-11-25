<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-xs font-semibold border border-indigo-500/20">
          <span>🏛️</span> NBA Tier-II Criterion 5 • 100 Marks
        </div>
        <h3 class="text-2xl font-black text-white mt-1 tracking-tight">
          Criterion 5: Faculty Information & Contributions
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">
          {{ criterionData.department_name || 'Department of Electronics and Communication Engineering' }} • Evaluated Cycle (CAY, CAYm1, CAYm2)
        </p>
      </div>

      <!-- Live Overall Score Badge -->
      <div class="flex items-center gap-4 bg-slate-900/90 border border-slate-800 p-3 rounded-2xl shadow-xl backdrop-blur-md">
        <div class="text-right">
          <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Total Claimed Assessment</div>
          <div class="text-2xl font-black text-emerald-400 font-mono">
            {{ criterionData.total_claimed_marks || 78.64 }} <span class="text-xs text-slate-400 font-normal">/ 100.0</span>
          </div>
        </div>
        <div class="h-10 w-px bg-slate-800"></div>
        <div class="text-left">
          <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Overall Compliance</div>
          <div class="text-sm font-bold text-indigo-300">
            {{ criterionData.percentage || 78.6 }}% Score
          </div>
          <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
            Optimal Compliance
          </span>
        </div>
      </div>
    </div>

    <!-- Sub-criterion Navigation Tabs -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-slate-800 scrollbar-none text-xs">
      <button
        @click="activeTab = 'overview'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'overview' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>📊</span> Dashboard Overview
      </button>

      <button
        @click="activeTab = 'sfr'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'sfr' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>5.1</span> SFR (30M)
        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold font-mono" :class="activeTab === 'sfr' ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400'">
          {{ c51.claimed_marks || 18.0 }}
        </span>
      </button>

      <button
        @click="activeTab = 'fqi'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'fqi' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>5.2</span> Qualification (25M)
        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold font-mono" :class="activeTab === 'fqi' ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400'">
          {{ c52.claimed_marks || 22.12 }}
        </span>
      </button>

      <button
        @click="activeTab = 'cadre'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'cadre' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>5.3</span> Cadre Ratio (25M)
        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold font-mono" :class="activeTab === 'cadre' ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400'">
          {{ c53.claimed_marks || 20.69 }}
        </span>
      </button>

      <button
        @click="activeTab = 'visiting'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'visiting' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>5.4</span> Visiting Faculty (10M)
        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold font-mono" :class="activeTab === 'visiting' ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400'">
          {{ c54.claimed_marks || 10.0 }}
        </span>
      </button>

      <button
        @click="activeTab = 'retention'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'retention' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>5.5</span> Retention (10M)
        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold font-mono" :class="activeTab === 'retention' ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400'">
          {{ c55.claimed_marks || 7.83 }}
        </span>
      </button>

      <button
        @click="activeTab = 'faculty_list'"
        class="px-3.5 py-2 rounded-lg font-semibold transition shrink-0 flex items-center gap-1.5"
        :class="activeTab === 'faculty_list' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
      >
        <span>📋</span> Table 5A: Faculty List ({{ facultyList.length || 123 }})
      </button>
    </div>

    <!-- TAB 0: DASHBOARD OVERVIEW -->
    <div v-if="activeTab === 'overview'" class="space-y-6">
      <!-- 5 Sub-criterion Score Cards -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- 5.1 -->
        <div @click="activeTab = 'sfr'" class="glass-card p-4 rounded-xl border border-slate-800 hover:border-indigo-500/50 cursor-pointer transition space-y-2">
          <div class="text-[10px] font-bold uppercase text-slate-400">5.1 SFR Ratio</div>
          <div class="text-2xl font-black text-emerald-400 font-mono">
            {{ c51.claimed_marks || 18.0 }} <span class="text-xs text-slate-400 font-normal">/ 30.0</span>
          </div>
          <p class="text-[11px] text-slate-400">
            3-Yr Average SFR: <strong class="text-indigo-300 font-mono">{{ c51.avg_sfr || 19.04 }}:1</strong>
          </p>
          <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-medium">
            Tier 4 (&lt; 21:1)
          </span>
        </div>

        <!-- 5.2 -->
        <div @click="activeTab = 'fqi'" class="glass-card p-4 rounded-xl border border-slate-800 hover:border-purple-500/50 cursor-pointer transition space-y-2">
          <div class="text-[10px] font-bold uppercase text-slate-400">5.2 Qualification</div>
          <div class="text-2xl font-black text-purple-400 font-mono">
            {{ c52.claimed_marks || 22.12 }} <span class="text-xs text-slate-400 font-normal">/ 25.0</span>
          </div>
          <p class="text-[11px] text-slate-400">
            3-Yr Average FQI: <strong class="text-purple-300 font-mono">{{ c52.avg_fqi || 22.12 }}</strong>
          </p>
          <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-400 border border-purple-500/20 font-medium">
            Ph.D. Indexed (X=71, Y=25)
          </span>
        </div>

        <!-- 5.3 -->
        <div @click="activeTab = 'cadre'" class="glass-card p-4 rounded-xl border border-slate-800 hover:border-blue-500/50 cursor-pointer transition space-y-2">
          <div class="text-[10px] font-bold uppercase text-slate-400">5.3 Cadre Ratio</div>
          <div class="text-2xl font-black text-blue-400 font-mono">
            {{ c53.claimed_marks || 20.69 }} <span class="text-xs text-slate-400 font-normal">/ 25.0</span>
          </div>
          <p class="text-[11px] text-slate-400">
            Cadre Proportion: <strong class="text-blue-300 font-mono">1 : 2 : 6 Target</strong>
          </p>
          <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 font-medium">
            AF1=6.67, AF2=15, AF3=68
          </span>
        </div>

        <!-- 5.4 -->
        <div @click="activeTab = 'visiting'" class="glass-card p-4 rounded-xl border border-slate-800 hover:border-amber-500/50 cursor-pointer transition space-y-2">
          <div class="text-[10px] font-bold uppercase text-slate-400">5.4 Visiting Faculty</div>
          <div class="text-2xl font-black text-amber-400 font-mono">
            {{ c54.claimed_marks || 10.0 }} <span class="text-xs text-slate-400 font-normal">/ 10.0</span>
          </div>
          <p class="text-[11px] text-slate-400">
            Annual Interaction: <strong class="text-amber-300 font-mono">170+ Hours/Yr</strong>
          </p>
          <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 font-medium">
            Full 10/10 Awarded
          </span>
        </div>

        <!-- 5.5 -->
        <div @click="activeTab = 'retention'" class="glass-card p-4 rounded-xl border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition space-y-2">
          <div class="text-[10px] font-bold uppercase text-slate-400">5.5 Faculty Retention</div>
          <div class="text-2xl font-black text-emerald-400 font-mono">
            {{ c55.claimed_marks || 7.83 }} <span class="text-xs text-slate-400 font-normal">/ 10.0</span>
          </div>
          <p class="text-[11px] text-slate-400">
            3-Yr Retention: <strong class="text-emerald-300 font-mono">{{ c55.avg_retention || 7.83 }} Pts</strong>
          </p>
          <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">
            60+ Faculty &gt; 4 Yrs
          </span>
        </div>
      </div>

      <!-- Quick Executive Summary Banner -->
      <div class="glass-card p-6 rounded-2xl border border-slate-800 bg-slate-900/60 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2">
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>📑</span> Official NBA Self-Assessment Report Status: Criterion 5
          </h4>
          <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
            Criterion 5 evaluates the strength, stability, and academic competence of the faculty. The department currently maintains 
            <strong>96 active faculty members</strong> (71 with Ph.D., 25 with M.Tech/ME), achieving a 3-year Student-Faculty Ratio of 
            <strong>19.04:1</strong>, meeting the NBA accreditation requirements across all 5 standard sub-criteria.
          </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
          <button @click="activeTab = 'faculty_list'" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 transition">
            View Table 5A (123 Faculty)
          </button>
          <button @click="$emit('navigate', 'criteria')" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-lg shadow-indigo-600/30">
            Criterion 1 - 10 Master
          </button>
        </div>
      </div>
    </div>

    <!-- TAB 1: 5.1 STUDENT-FACULTY RATIO -->
    <div v-if="activeTab === 'sfr'" class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>5.1</span> Student-Faculty Ratio (30 Marks)
          </h4>
          <p class="text-xs text-slate-400">
            Formula: <code class="text-indigo-300 font-mono">SFR = S / (F - FF)</code> evaluated at Department level considering all UG and PG programs.
          </p>
        </div>
        <div class="text-right">
          <span class="text-xs text-slate-400">Awarded Marks:</span>
          <span class="ml-2 text-xl font-black text-emerald-400 font-mono">{{ c51.claimed_marks }} / 30.0</span>
        </div>
      </div>

      <!-- Table 5.1.1: Student Intake and Admission Details -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.1.1: Calculation of No. of Students Admitted Through Lateral Entry or Leftover Seats
          </h5>
          <span class="text-[11px] text-indigo-400 font-medium">Lateral Cap Rule: Max 110% of SA</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">Program & Academic Year</th>
                <th class="p-3 text-right">Sanctioned Intake (SA)</th>
                <th class="p-3 text-right">Admitted in 1st Yr (N1)</th>
                <th class="p-3 text-right">Unfilled in 1st Yr</th>
                <th class="p-3 text-right">Admitted in 2nd Yr (L=a+b)</th>
                <th class="p-3 text-right font-bold text-white">Students Considered (ST = SA + L)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300">
              <template v-for="prog in c51.table_5_1_1?.programs" :key="prog.code">
                <tr class="bg-slate-900/90 font-bold text-indigo-300">
                  <td colspan="6" class="p-2.5 text-xs">{{ prog.name }} ({{ prog.code }})</td>
                </tr>
                <tr v-for="row in prog.years" :key="row.year" class="hover:bg-slate-800/40">
                  <td class="p-2.5 pl-6 font-medium text-slate-200">{{ row.year }}</td>
                  <td class="p-2.5 text-right font-mono">{{ row.sa }}</td>
                  <td class="p-2.5 text-right font-mono">{{ row.n1 }}</td>
                  <td class="p-2.5 text-right font-mono text-slate-500">{{ row.unfilled }}</td>
                  <td class="p-2.5 text-right font-mono text-amber-400">{{ row.l || 0 }}</td>
                  <td class="p-2.5 text-right font-mono font-bold text-emerald-400">{{ row.st }}</td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Table 5.1.2: Student-Faculty Ratio Table -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.1.2: Student-Faculty Ratio Details (Department Level)
          </h5>
          <span class="text-xs font-bold text-emerald-400 font-mono">3-Year Average SFR: {{ c51.avg_sfr }}:1</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">Parameter / Cohort Category</th>
                <th class="p-3 text-right">CAY (2025-26)</th>
                <th class="p-3 text-right">CAYm1 (2024-25)</th>
                <th class="p-3 text-right">CAYm2 (2023-24)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300 font-mono">
              <tr>
                <td class="p-2.5 font-sans text-slate-200">UG1: B.E. (Electronics & Comm. Engg.) [B+C+D]</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAY?.ug1_total }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm1?.ug1_total }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm2?.ug1_total }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-200">UG2: B.E. (Electronics & Computer Engg.) [B+C+D]</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAY?.ug2_total }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm1?.ug2_total }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm2?.ug2_total }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-200">UG3: B.E. (VLSI Design and Technology) [B+C+D]</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAY?.ug3_total }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm1?.ug3_total }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm2?.ug3_total }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-200">PG Programs (M.Tech VLSI & ME ECE) [A+B]</td>
                <td class="p-2.5 text-right font-bold text-white">{{ (c51.table_5_1_2?.CAY?.pg1_total || 0) + (c51.table_5_1_2?.CAY?.pg2_total || 0) }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ (c51.table_5_1_2?.CAYm1?.pg1_total || 0) + (c51.table_5_1_2?.CAYm1?.pg2_total || 0) }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ (c51.table_5_1_2?.CAYm2?.pg1_total || 0) + (c51.table_5_1_2?.CAYm2?.pg2_total || 0) }}</td>
              </tr>
              <tr class="bg-indigo-950/30 text-indigo-300 font-bold font-sans">
                <td class="p-2.5">Total Students in Department (S = DS + AS)</td>
                <td class="p-2.5 text-right font-mono text-emerald-400">{{ c51.table_5_1_2?.CAY?.s }}</td>
                <td class="p-2.5 text-right font-mono text-emerald-400">{{ c51.table_5_1_2?.CAYm1?.s }}</td>
                <td class="p-2.5 text-right font-mono text-emerald-400">{{ c51.table_5_1_2?.CAYm2?.s }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-200">Department Faculty (DF)</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAY?.df }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm1?.df }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c51.table_5_1_2?.CAYm2?.df }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-200">Faculty with 100% 1st-Year Teaching Load (FF)</td>
                <td class="p-2.5 text-right text-slate-500">{{ c51.table_5_1_2?.CAY?.ff || 0 }}</td>
                <td class="p-2.5 text-right text-slate-500">{{ c51.table_5_1_2?.CAYm1?.ff || 0 }}</td>
                <td class="p-2.5 text-right text-slate-500">{{ c51.table_5_1_2?.CAYm2?.ff || 0 }}</td>
              </tr>
              <tr class="bg-slate-900 font-bold font-sans">
                <td class="p-2.5 text-white">Total Eligible Faculty (TF = F - FF)</td>
                <td class="p-2.5 text-right font-mono text-indigo-400">{{ c51.table_5_1_2?.CAY?.tf }}</td>
                <td class="p-2.5 text-right font-mono text-indigo-400">{{ c51.table_5_1_2?.CAYm1?.tf }}</td>
                <td class="p-2.5 text-right font-mono text-indigo-400">{{ c51.table_5_1_2?.CAYm2?.tf }}</td>
              </tr>
              <tr class="bg-emerald-950/20 font-bold text-emerald-300 font-sans">
                <td class="p-2.5">Student-Faculty Ratio: SFR = S / (F - FF)</td>
                <td class="p-2.5 text-right font-mono text-base">{{ c51.table_5_1_2?.CAY?.sfr }}:1</td>
                <td class="p-2.5 text-right font-mono text-base">{{ c51.table_5_1_2?.CAYm1?.sfr }}:1</td>
                <td class="p-2.5 text-right font-mono text-base">{{ c51.table_5_1_2?.CAYm2?.sfr }}:1</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-950 font-bold text-white">
              <tr>
                <td class="p-3 uppercase text-[11px] text-slate-400">3-Year Average SFR:</td>
                <td colspan="3" class="p-3 text-right text-lg text-emerald-400 font-mono">
                  {{ c51.avg_sfr }}:1 &nbsp;→&nbsp; {{ c51.claimed_marks }} / 30.0 Marks
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: 5.2 FACULTY QUALIFICATION (FQI) -->
    <div v-if="activeTab === 'fqi'" class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>5.2</span> Faculty Qualification Index (25 Marks)
          </h4>
          <p class="text-xs text-slate-400">
            Formula: <code class="text-purple-300 font-mono">FQI = 2.5 × [(10X + 4Y) / RF]</code> where <code class="text-purple-300 font-mono">X = Ph.D. Faculty, Y = M.Tech/ME Faculty, RF = S / 20</code>
          </p>
        </div>
        <div class="text-right">
          <span class="text-xs text-slate-400">Claimed Marks:</span>
          <span class="ml-2 text-xl font-black text-purple-400 font-mono">{{ c52.claimed_marks }} / 25.0</span>
        </div>
      </div>

      <!-- Table 5.2.1 -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.2.1: Faculty Qualification Index across 3 Assessment Years
          </h5>
          <span class="text-xs text-purple-400 font-mono font-bold">Average Assessment: {{ c52.avg_fqi }} / 25.0</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">Academic Year</th>
                <th class="p-3 text-right">Total Faculty (T)</th>
                <th class="p-3 text-right">Ph.D. Faculty (X)</th>
                <th class="p-3 text-right">M.Tech / ME Faculty (Y)</th>
                <th class="p-3 text-right">Required Faculty RF (S/20)</th>
                <th class="p-3 text-right font-bold text-white">FQI = 2.5 × [(10X + 4Y) / RF]</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300 font-mono">
              <tr v-for="(row, key) in c52.table_5_2_1" :key="key" class="hover:bg-slate-800/40">
                <td class="p-3 font-sans font-bold text-white">{{ row.year_name }}</td>
                <td class="p-3 text-right font-bold text-slate-200">{{ row.t }}</td>
                <td class="p-3 text-right font-bold text-emerald-400">{{ row.x }}</td>
                <td class="p-3 text-right font-bold text-indigo-400">{{ row.y }}</td>
                <td class="p-3 text-right font-bold text-amber-400">{{ row.rf }}</td>
                <td class="p-3 text-right font-bold text-purple-400 text-sm">{{ row.fqi }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-950 font-bold text-white">
              <tr>
                <td colspan="5" class="p-3 text-right text-slate-400 uppercase text-[11px]">3-Year Average FQI Assessment:</td>
                <td class="p-3 text-right text-lg text-purple-400 font-mono">{{ c52.avg_fqi }} / 25.0</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 3: 5.3 FACULTY CADRE PROPORTION -->
    <div v-if="activeTab === 'cadre'" class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>5.3</span> Faculty Cadre Proportion (25 Marks)
          </h4>
          <p class="text-xs text-slate-400">
            Cadre Target: <code class="text-blue-300 font-mono">1 : 2 : 6 (Professors : Associate Professors : Assistant Professors)</code>
          </p>
        </div>
        <div class="text-right">
          <span class="text-xs text-slate-400">Claimed Marks:</span>
          <span class="ml-2 text-xl font-black text-blue-400 font-mono">{{ c53.claimed_marks }} / 25.0</span>
        </div>
      </div>

      <!-- Table 5.3.1 -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.3.1: Faculty Cadre Proportion Details
          </h5>
          <span class="text-xs text-blue-400 font-mono font-bold">Cadre Score: {{ c53.claimed_marks }} / 25.0</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th rowspan="2" class="p-3 border-r border-slate-800">Year</th>
                <th rowspan="2" class="p-3 text-right border-r border-slate-800">Total (T)</th>
                <th colspan="2" class="p-2 text-center border-r border-slate-800 text-indigo-300">Professors (RF1 / AF1)</th>
                <th colspan="2" class="p-2 text-center border-r border-slate-800 text-purple-300">Associate Professors (RF2 / AF2)</th>
                <th colspan="2" class="p-2 text-center text-emerald-300">Assistant Professors (RF3 / AF3)</th>
              </tr>
              <tr class="border-t border-slate-800">
                <th class="p-2 text-right text-[9px] text-slate-400">Req (RF1)</th>
                <th class="p-2 text-right text-[9px] text-indigo-300 border-r border-slate-800">Avail (AF1)</th>
                <th class="p-2 text-right text-[9px] text-slate-400">Req (RF2)</th>
                <th class="p-2 text-right text-[9px] text-purple-300 border-r border-slate-800">Avail (AF2)</th>
                <th class="p-2 text-right text-[9px] text-slate-400">Req (RF3)</th>
                <th class="p-2 text-right text-[9px] text-emerald-300">Avail (AF3)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300 font-mono">
              <tr v-for="(row, key) in c53.table_5_3_1" :key="key" class="hover:bg-slate-800/40">
                <td class="p-3 font-sans font-bold text-white border-r border-slate-800">{{ row.year_name }}</td>
                <td class="p-3 text-right text-slate-200 border-r border-slate-800">{{ row.t }}</td>
                <td class="p-3 text-right text-slate-400">{{ row.rf1 }}</td>
                <td class="p-3 text-right font-bold text-indigo-300 border-r border-slate-800">{{ row.af1 }}</td>
                <td class="p-3 text-right text-slate-400">{{ row.rf2 }}</td>
                <td class="p-3 text-right font-bold text-purple-300 border-r border-slate-800">{{ row.af2 }}</td>
                <td class="p-3 text-right text-slate-400">{{ row.rf3 }}</td>
                <td class="p-3 text-right font-bold text-emerald-300">{{ row.af3 }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-950 font-bold text-white font-mono">
              <tr>
                <td class="p-3 font-sans uppercase text-[11px] text-slate-400 border-r border-slate-800">3-Year Averages:</td>
                <td class="p-3 text-right border-r border-slate-800 text-slate-300">{{ c53.averages?.t }}</td>
                <td class="p-3 text-right text-slate-400">{{ c53.averages?.rf1 }}</td>
                <td class="p-3 text-right text-indigo-300 border-r border-slate-800">{{ c53.averages?.af1 }}</td>
                <td class="p-3 text-right text-slate-400">{{ c53.averages?.rf2 }}</td>
                <td class="p-3 text-right text-purple-300 border-r border-slate-800">{{ c53.averages?.af2 }}</td>
                <td class="p-3 text-right text-slate-400">{{ c53.averages?.rf3 }}</td>
                <td class="p-3 text-right text-emerald-300">{{ c53.averages?.af3 }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Formula Breakdown -->
      <div class="glass-card p-4 rounded-xl border border-slate-800 bg-slate-900/60 space-y-2">
        <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider">NBA Cadre Proportion Formula Calculation</h5>
        <div class="p-3 rounded-lg bg-slate-950 font-mono text-xs text-blue-300 space-y-1">
          <div>Cadre Score = [ (AF1 / RF1) + (AF2 / RF2 × 0.6) + (AF3 / RF3 × 0.4) ] × 12.5</div>
          <div class="text-slate-400">
            = [ ({{ c53.averages?.af1 }} / {{ c53.averages?.rf1 }}) + ({{ c53.averages?.af2 }} / {{ c53.averages?.rf2 }} × 0.6) + ({{ c53.averages?.af3 }} / {{ c53.averages?.rf3 }} × 0.4) ] × 12.5
          </div>
          <div class="text-emerald-400 font-bold">
            = [ {{ c53.breakdown_calculation?.prof_component }} + {{ c53.breakdown_calculation?.assoc_component }} + {{ c53.breakdown_calculation?.asst_component }} ] × 12.5 = {{ c53.claimed_marks }} / 25.0 Marks
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 4: 5.4 VISITING / ADJUNCT FACULTY -->
    <div v-if="activeTab === 'visiting'" class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>5.4</span> Visiting / Adjunct Faculty / Professor of Practice (10 Marks)
          </h4>
          <p class="text-xs text-slate-400">
            Provision: 1 Mark • Minimum 50 hours/year earns 3 marks/year across 3 years (3 × 3 = 9 Marks) • Total Max = 10 Marks.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="showAddVisitingModal = true" class="px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-amber-600/30">
            <span>+</span> Add Visiting Faculty
          </button>
          <div class="text-right">
            <span class="text-xs text-slate-400">Awarded:</span>
            <span class="ml-2 text-xl font-black text-amber-400 font-mono">{{ c54.claimed_marks }} / 10.0</span>
          </div>
        </div>
      </div>

      <!-- Table 5.4.1 -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.4.1: List of Visiting / Adjunct Faculty & Interaction Loads
          </h5>
          <span class="text-xs text-amber-400 font-medium">Provision: +1.0 Mark | Total: {{ c54.claimed_marks }}/10</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">Year / Faculty Member</th>
                <th class="p-3">Designation & Organization</th>
                <th class="p-3">Course Handled / Contribution</th>
                <th class="p-3 text-right">Interaction Hours</th>
                <th class="p-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300">
              <template v-for="(yData, key) in c54.table_5_4_1" :key="key">
                <tr class="bg-slate-900/90 font-bold text-amber-300">
                  <td colspan="3" class="p-2.5 text-xs">{{ yData.year_name }}</td>
                  <td class="p-2.5 text-right font-mono text-emerald-400">{{ yData.total_hours }} Hours</td>
                  <td class="p-2.5 text-right text-[10px] text-slate-400">
                    <span v-if="yData.qualifies" class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      ✓ Qualified (3 Marks)
                    </span>
                    <span v-else class="px-2 py-0.5 rounded bg-rose-500/10 text-rose-400">
                      &lt; 50 Hours
                    </span>
                  </td>
                </tr>
                <tr v-for="vf in yData.faculty" :key="vf.id" class="hover:bg-slate-800/40">
                  <td class="p-2.5 pl-6 font-bold text-white">{{ vf.name }}</td>
                  <td class="p-2.5 text-slate-300">{{ vf.designation }}, <span class="text-slate-400">{{ vf.organization }}</span></td>
                  <td class="p-2.5 text-slate-300">{{ vf.course_name }}</td>
                  <td class="p-2.5 text-right font-mono font-bold text-amber-300">{{ vf.interaction_hours }} hrs</td>
                  <td class="p-2.5 text-right">
                    <button @click="deleteVisiting(vf.id)" class="text-rose-400 hover:text-rose-300 text-[11px] underline">
                      Delete
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Table 5.4.2: Summary -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.4.2: Summary of Visiting / Adjunct Faculty Hours & Scores
          </h5>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-800 text-center p-4 bg-slate-950/60">
          <div class="p-2 space-y-1">
            <div class="text-[10px] text-slate-400 uppercase font-bold">Policy Provision</div>
            <div class="text-xl font-bold text-emerald-400 font-mono">+1.0</div>
            <div class="text-[10px] text-slate-500">Visiting Faculty Appointed</div>
          </div>
          <div v-for="(sRow, sKey) in c54.table_5_4_2" :key="sKey" class="p-2 space-y-1">
            <div class="text-[10px] text-slate-400 uppercase font-bold">{{ sRow.year_name }}</div>
            <div class="text-xl font-bold text-amber-400 font-mono">{{ sRow.score_awarded }} / 3.0</div>
            <div class="text-[10px] text-slate-500">{{ sRow.total_hours }} Hours (Target: ≥50)</div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 5: 5.5 FACULTY RETENTION -->
    <div v-if="activeTab === 'retention'" class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>5.5</span> Faculty Retention Ratio (10 Marks)
          </h4>
          <p class="text-xs text-slate-400">
            Formula: <code class="text-emerald-300 font-mono">FR = [((A×0) + (B×1) + (C×2) + (D×3) + (E×4)) / RF] × 2.50</code> (limited to 10)
          </p>
        </div>
        <div class="text-right">
          <span class="text-xs text-slate-400">Claimed Marks:</span>
          <span class="ml-2 text-xl font-black text-emerald-400 font-mono">{{ c55.claimed_marks }} / 10.0</span>
        </div>
      </div>

      <!-- Table 5.5.1 -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
          <h5 class="text-xs font-bold text-white uppercase tracking-wider">
            Table 5.5.1: Faculty Retention Ratio across Evaluation Cycle
          </h5>
          <span class="text-xs text-emerald-400 font-mono font-bold">Average FR: {{ c55.avg_retention }} / 10.0</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">Item / Experience Category</th>
                <th class="p-3 text-right">2025-26 (CAY)</th>
                <th class="p-3 text-right">2024-25 (CAYm1)</th>
                <th class="p-3 text-right">2023-24 (CAYm2)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300 font-mono">
              <tr>
                <td class="p-2.5 font-sans text-slate-200">RF = Required Faculty in Dept (S / 20)</td>
                <td class="p-2.5 text-right font-bold text-amber-400">{{ c55.table_5_5_1?.CAY?.rf }}</td>
                <td class="p-2.5 text-right font-bold text-amber-400">{{ c55.table_5_5_1?.CAYm1?.rf }}</td>
                <td class="p-2.5 text-right font-bold text-amber-400">{{ c55.table_5_5_1?.CAYm2?.rf }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-200">AF = Available Faculty Members in Dept</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c55.table_5_5_1?.CAY?.af }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c55.table_5_5_1?.CAYm1?.af }}</td>
                <td class="p-2.5 text-right font-bold text-white">{{ c55.table_5_5_1?.CAYm2?.af }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-300 pl-6">A = Experience &lt; 1 Year (Weight × 0)</td>
                <td class="p-2.5 text-right text-slate-400">{{ c55.table_5_5_1?.CAY?.a }}</td>
                <td class="p-2.5 text-right text-slate-400">{{ c55.table_5_5_1?.CAYm1?.a }}</td>
                <td class="p-2.5 text-right text-slate-400">{{ c55.table_5_5_1?.CAYm2?.a }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-300 pl-6">B = Experience 1 to 2 Years (Weight × 1)</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAY?.b }}</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAYm1?.b }}</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAYm2?.b }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-300 pl-6">C = Experience 2 to 3 Years (Weight × 2)</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAY?.c }}</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAYm1?.c }}</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAYm2?.c }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-300 pl-6">D = Experience 3 to 4 Years (Weight × 3)</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAY?.d }}</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAYm1?.d }}</td>
                <td class="p-2.5 text-right text-slate-300">{{ c55.table_5_5_1?.CAYm2?.d }}</td>
              </tr>
              <tr>
                <td class="p-2.5 font-sans text-slate-300 pl-6">E = Experience &gt; 4 Years (Weight × 4)</td>
                <td class="p-2.5 text-right font-bold text-emerald-400">{{ c55.table_5_5_1?.CAY?.e }}</td>
                <td class="p-2.5 text-right font-bold text-emerald-400">{{ c55.table_5_5_1?.CAYm1?.e }}</td>
                <td class="p-2.5 text-right font-bold text-emerald-400">{{ c55.table_5_5_1?.CAYm2?.e }}</td>
              </tr>
              <tr class="bg-slate-900 font-bold font-sans">
                <td class="p-2.5 text-white">Weighted Retention Points Sum</td>
                <td class="p-2.5 text-right font-mono text-indigo-300">{{ c55.table_5_5_1?.CAY?.points }} Pts</td>
                <td class="p-2.5 text-right font-mono text-indigo-300">{{ c55.table_5_5_1?.CAYm1?.points }} Pts</td>
                <td class="p-2.5 text-right font-mono text-indigo-300">{{ c55.table_5_5_1?.CAYm2?.points }} Pts</td>
              </tr>
              <tr class="bg-emerald-950/20 font-bold text-emerald-300 font-sans">
                <td class="p-2.5">Annual Retention Score (FR)</td>
                <td class="p-2.5 text-right font-mono text-base">{{ c55.table_5_5_1?.CAY?.fr }}</td>
                <td class="p-2.5 text-right font-mono text-base">{{ c55.table_5_5_1?.CAYm1?.fr }}</td>
                <td class="p-2.5 text-right font-mono text-base">{{ c55.table_5_5_1?.CAYm2?.fr }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-950 font-bold text-white font-mono">
              <tr>
                <td class="p-3 font-sans uppercase text-[11px] text-slate-400">Average FR for 3 Years:</td>
                <td colspan="3" class="p-3 text-right text-lg text-emerald-400">
                  {{ c55.avg_retention }} / 10.0 Marks
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 6: TABLE 5A: FULL FACULTY DIRECTORY -->
    <div v-if="activeTab === 'faculty_list'" class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <span>📋</span> Table 5A: Faculty List for Evaluated Academic Years
          </h4>
          <p class="text-xs text-slate-400">
            Total active & historical faculty members in Department of ECE ({{ facultyList.length }} total records).
          </p>
        </div>

        <div class="flex items-center gap-2">
          <!-- Search input -->
          <input
            type="text"
            v-model="facultySearch"
            placeholder="Search faculty name, degree, area..."
            class="bg-slate-900 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:border-indigo-500 outline-none w-56"
          />

          <!-- Designation filter -->
          <select
            v-model="facultyFilterCadre"
            class="bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:border-indigo-500 outline-none"
          >
            <option value="">All Cadres</option>
            <option value="Professor">Professors</option>
            <option value="Associate Professor">Associate Professors</option>
            <option value="Assistant Professor">Assistant Professors</option>
          </select>

          <!-- Add Faculty Button -->
          <button
            @click="showAddFacultyModal = true"
            class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-indigo-600/30"
          >
            <span>+</span> Add Faculty
          </button>
        </div>
      </div>

      <!-- Table 5A -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
              <tr>
                <th class="p-3 w-12 text-center">Sr.</th>
                <th class="p-3">Faculty Code / Name</th>
                <th class="p-3">Highest Degree</th>
                <th class="p-3">University</th>
                <th class="p-3">Specialization</th>
                <th class="p-3">Joining Date</th>
                <th class="p-3">Experience</th>
                <th class="p-3">Designation</th>
                <th class="p-3">Association</th>
                <th class="p-3 text-center">Active</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300">
              <tr v-for="(fac, index) in filteredFacultyList" :key="fac.id" class="hover:bg-slate-800/40">
                <td class="p-3 text-center font-mono text-slate-500">{{ index + 1 }}</td>
                <td class="p-3 font-semibold text-white">
                  {{ fac.name }}
                  <div class="text-[10px] text-slate-500 font-mono">{{ fac.faculty_id }}</div>
                </td>
                <td class="p-3 font-medium text-emerald-400">{{ fac.highest_degree }}</td>
                <td class="p-3 text-slate-300">{{ fac.university_awarded || 'TIET, Patiala' }}</td>
                <td class="p-3 text-slate-300 max-w-xs truncate" :title="fac.degree_specialization">{{ fac.degree_specialization || 'Electronics Engineering' }}</td>
                <td class="p-3 font-mono text-slate-400">{{ fac.joining_date }}</td>
                <td class="p-3 font-mono text-slate-200">{{ fac.teaching_experience_years }} Yrs</td>
                <td class="p-3 font-medium text-indigo-300">{{ getDesignation(fac) }}</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                        :class="fac.association_type === 'Regular' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-slate-800 text-slate-300'">
                    {{ fac.association_type || 'Regular' }}
                  </span>
                </td>
                <td class="p-3 text-center">
                  <span v-if="fac.is_active" class="text-emerald-400 text-sm font-bold">✓</span>
                  <span v-else class="text-rose-400 text-xs">Left</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL: ADD VISITING FACULTY -->
    <div v-if="showAddVisitingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm">
      <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-slate-900 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span>+</span> Add Visiting / Adjunct Faculty Member
          </h3>
          <button @click="showAddVisitingModal = false" class="text-slate-400 hover:text-white text-xl">✕</button>
        </div>

        <form @submit.prevent="submitVisitingFaculty" class="space-y-3 text-xs">
          <div>
            <label class="block text-slate-400 mb-1">Faculty / Expert Name *</label>
            <input type="text" v-model="visitingForm.name" required placeholder="e.g. Prof. Sanjeev Bedi" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-400 mb-1">Designation *</label>
              <input type="text" v-model="visitingForm.designation" required placeholder="e.g. Professor / Adjunct" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500" />
            </div>
            <div>
              <label class="block text-slate-400 mb-1">Organization / Institution *</label>
              <input type="text" v-model="visitingForm.organization" required placeholder="e.g. University of Waterloo" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-400 mb-1">Course Name Handled *</label>
              <input type="text" v-model="visitingForm.course_name" required placeholder="e.g. Capstone Projects" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500" />
            </div>
            <div>
              <label class="block text-slate-400 mb-1">Course Code</label>
              <input type="text" v-model="visitingForm.course_code" placeholder="e.g. UEC797" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-400 mb-1">Contribution Type *</label>
              <select v-model="visitingForm.contribution_type" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500">
                <option value="Teaching & Project Guidance">Teaching & Project Guidance</option>
                <option value="Guest Lecture & Practical Labs">Guest Lecture & Practical Labs</option>
                <option value="Research Collaboration">Research Collaboration</option>
              </select>
            </div>
            <div>
              <label class="block text-slate-400 mb-1">Interaction Hours *</label>
              <input type="number" v-model="visitingForm.interaction_hours" required min="1" placeholder="e.g. 60" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500 font-mono" />
            </div>
          </div>

          <div>
            <label class="block text-slate-400 mb-1">Academic Assessment Year *</label>
            <select v-model="visitingForm.academic_year_id" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-amber-500">
              <option v-for="y in assessmentYears" :key="y.id" :value="y.id">
                {{ y.name }} ({{ y.type }})
              </option>
            </select>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
            <button type="button" @click="showAddVisitingModal = false" class="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700">Cancel</button>
            <button type="submit" :disabled="submittingVisiting" class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold disabled:opacity-50">
              {{ submittingVisiting ? 'Saving...' : 'Save Visiting Record' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL: ADD FACULTY MEMBER -->
    <div v-if="showAddFacultyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm">
      <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-slate-900 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span>+</span> Add Faculty Member to Table 5A
          </h3>
          <button @click="showAddFacultyModal = false" class="text-slate-400 hover:text-white text-xl">✕</button>
        </div>

        <form @submit.prevent="submitFaculty" class="space-y-3 text-xs">
          <div>
            <label class="block text-slate-400 mb-1">Full Name *</label>
            <input type="text" v-model="facultyForm.name" required placeholder="e.g. Professor L or Dr. Name" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-400 mb-1">Cadre *</label>
              <select v-model="facultyForm.cadre" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500">
                <option value="Professor">Professor</option>
                <option value="Associate Professor">Associate Professor</option>
                <option value="Assistant Professor">Assistant Professor</option>
              </select>
            </div>
            <div>
              <label class="block text-slate-400 mb-1">Highest Degree *</label>
              <select v-model="facultyForm.highest_degree" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500">
                <option value="Ph.D.">Ph.D.</option>
                <option value="Postdoc">Postdoc</option>
                <option value="M.Tech">M.Tech</option>
                <option value="M.E.">M.E.</option>
                <option value="M.Sc.">M.Sc.</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-400 mb-1">Date of Joining *</label>
              <input type="date" v-model="facultyForm.joining_date" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500" />
            </div>
            <div>
              <label class="block text-slate-400 mb-1">Nature of Association *</label>
              <select v-model="facultyForm.association_type" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500">
                <option value="Regular">Regular</option>
                <option value="Contractual Fulltime">Contractual Fulltime</option>
                <option value="Adjunct">Adjunct</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-slate-400 mb-1">Specialization</label>
            <input type="text" v-model="facultyForm.degree_specialization" placeholder="e.g. VLSI Design / Wireless Communication" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500" />
          </div>

          <div>
            <label class="block text-slate-400 mb-1">University Awarded</label>
            <input type="text" v-model="facultyForm.university_awarded" placeholder="e.g. TIET / IIT Roorkee" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-white outline-none focus:border-indigo-500" />
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
            <button type="button" @click="showAddFacultyModal = false" class="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700">Cancel</button>
            <button type="submit" :disabled="submittingFaculty" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold disabled:opacity-50">
              {{ submittingFaculty ? 'Adding...' : 'Add Faculty Member' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CriterionFiveView',
  props: {
    programId: {
      type: [Number, String],
      default: 1,
    },
    sar: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      activeTab: 'overview',
      loading: true,
      criterionData: {},
      c51: {},
      c52: {},
      c53: {},
      c54: {},
      c55: {},
      facultyList: [],
      facultySearch: '',
      facultyFilterCadre: '',
      assessmentYears: [],

      // Visiting Modal
      showAddVisitingModal: false,
      submittingVisiting: false,
      visitingForm: {
        name: '',
        designation: '',
        organization: '',
        course_name: '',
        course_code: '',
        contribution_type: 'Teaching & Project Guidance',
        interaction_hours: 60,
        academic_year_id: 2, // CAYm1 default
      },

      // Faculty Modal
      showAddFacultyModal: false,
      submittingFaculty: false,
      facultyForm: {
        name: '',
        cadre: 'Assistant Professor',
        designation: 'Assistant Professor',
        highest_degree: 'Ph.D.',
        joining_date: '2023-08-01',
        association_type: 'Regular',
        degree_specialization: '',
        university_awarded: '',
      },
    };
  },
  computed: {
    filteredFacultyList() {
      let list = this.facultyList;
      if (this.facultyFilterCadre) {
        list = list.filter(f => {
          const desig = (f.name + ' ' + (f.highest_degree || '')).toLowerCase();
          if (this.facultyFilterCadre === 'Professor') {
            return f.name.startsWith('Professor');
          } else if (this.facultyFilterCadre === 'Associate Professor') {
            return f.name.startsWith('Associate Professor');
          } else if (this.facultyFilterCadre === 'Assistant Professor') {
            return f.name.startsWith('Assistant Professor');
          }
          return true;
        });
      }

      if (this.facultySearch) {
        const q = this.facultySearch.toLowerCase();
        list = list.filter(f =>
          (f.name && f.name.toLowerCase().includes(q)) ||
          (f.highest_degree && f.highest_degree.toLowerCase().includes(q)) ||
          (f.degree_specialization && f.degree_specialization.toLowerCase().includes(q)) ||
          (f.university_awarded && f.university_awarded.toLowerCase().includes(q))
        );
      }

      return list;
    },
  },
  mounted() {
    this.fetchData();
    this.fetchFaculty();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const res = await axios.get('/api/v1/sar/criterion-5');
        if (res.data && res.data.success) {
          this.criterionData = res.data.data;
          this.c51 = this.criterionData.sub_criteria?.c51 || {};
          this.c52 = this.criterionData.sub_criteria?.c52 || {};
          this.c53 = this.criterionData.sub_criteria?.c53 || {};
          this.c54 = this.criterionData.sub_criteria?.c54 || {};
          this.c55 = this.criterionData.sub_criteria?.c55 || {};
          this.assessmentYears = this.criterionData.assessment_years || [];
        }
      } catch (err) {
        console.error('Failed to load Criterion 5 data:', err);
      } finally {
        this.loading = false;
      }
    },

    async fetchFaculty() {
      try {
        const res = await axios.get('/api/v1/sar/criterion-5/faculty');
        if (res.data && res.data.success) {
          this.facultyList = res.data.data;
        }
      } catch (err) {
        console.error('Failed to load faculty list:', err);
      }
    },

    getDesignation(fac) {
      if (fac.name.startsWith('Professor')) return 'Professor';
      if (fac.name.startsWith('Associate Professor')) return 'Associate Professor';
      return 'Assistant Professor';
    },

    async submitVisitingFaculty() {
      this.submittingVisiting = true;
      try {
        const res = await axios.post('/api/v1/sar/criterion-5/visiting-faculty', this.visitingForm);
        if (res.data && res.data.success) {
          alert('Visiting faculty record saved!');
          this.showAddVisitingModal = false;
          this.fetchData();
        }
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to save visiting faculty.');
      } finally {
        this.submittingVisiting = false;
      }
    },

    async deleteVisiting(id) {
      if (!confirm('Are you sure you want to delete this visiting faculty record?')) return;
      try {
        const res = await axios.delete(`/api/v1/sar/criterion-5/visiting-faculty/${id}`);
        if (res.data && res.data.success) {
          this.fetchData();
        }
      } catch (err) {
        alert('Failed to delete visiting faculty.');
      }
    },

    async submitFaculty() {
      this.submittingFaculty = true;
      try {
        this.facultyForm.designation = this.facultyForm.cadre;
        const res = await axios.post('/api/v1/sar/criterion-5/faculty', this.facultyForm);
        if (res.data && res.data.success) {
          alert('Faculty member added successfully!');
          this.showAddFacultyModal = false;
          this.fetchFaculty();
          this.fetchData();
        }
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to add faculty member.');
      } finally {
        this.submittingFaculty = false;
      }
    },
  },
};
</script>
