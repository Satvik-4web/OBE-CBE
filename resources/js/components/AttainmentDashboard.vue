<template>
  <div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-xs font-semibold border border-emerald-500/20">
          <AppIcon name="target" size="12" />
          <span>Criterion 3.2 & 3.3 • 120 Marks</span>
        </div>
        <h3 class="text-xl font-bold text-white mt-1">
          PO & PSO Attainment Evaluation & Marks Entry
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">
          Formula: <code class="text-emerald-300 font-mono">Final PO Attainment = (0.80 × Direct) + (0.20 × Indirect)</code>
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center space-x-3">
        <button @click="showMarksModal = true"
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
          Record Assessment Marks
        </button>

        <div class="flex items-center space-x-3 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl">
          <div>
            <div class="text-[10px] text-slate-400 uppercase font-bold">Overall PO Score</div>
            <div class="text-lg font-black text-emerald-400">
              {{ attainment?.overall_po_average || 2.62 }} <span class="text-xs text-slate-400 font-normal">/ 3.0</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="successMsg" class="p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between">
      <span>✅ {{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-emerald-400 hover:text-white">✕</button>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex space-x-6 border-b border-slate-800 pb-2">
      <button @click="activeTab = 'po_pso'" :class="activeTab === 'po_pso' ? 'border-b-2 border-emerald-500 text-white' : 'text-slate-400 hover:text-slate-200'" class="pb-2 text-sm font-bold uppercase tracking-wider transition">PO & PSO Attainment</button>
      <button @click="activeTab = 'co_trend'" :class="activeTab === 'co_trend' ? 'border-b-2 border-emerald-500 text-white' : 'text-slate-400 hover:text-slate-200'" class="pb-2 text-sm font-bold uppercase tracking-wider transition">CO Trend</button>
      <button @click="activeTab = 'cie_heatmap'" :class="activeTab === 'cie_heatmap' ? 'border-b-2 border-emerald-500 text-white' : 'text-slate-400 hover:text-slate-200'" class="pb-2 text-sm font-bold uppercase tracking-wider transition">CIE Heatmap</button>
      <button @click="activeTab = 'alerts'" :class="activeTab === 'alerts' ? 'border-b-2 border-emerald-500 text-white' : 'text-slate-400 hover:text-slate-200'" class="pb-2 text-sm font-bold uppercase tracking-wider transition flex items-center gap-2">
        Low-Attainment Alerts
        <span v-if="alertsData && alertsData.length > 0" class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px]">{{ alertsData.length }}</span>
      </button>
    </div>

    <!-- PO & PSO Tab -->
    <div v-if="activeTab === 'po_pso'" class="space-y-6">
      <!-- PO Attainment Table -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between">
          <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider">
            Program Outcome (PO1 to PO12) Attainment Matrix (Direct 80% + Indirect 20%)
          </h4>
          <span class="text-xs text-slate-400">Benchmark Target: 2.50 / 3.0 (83.3%)</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950/80 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">PO Code</th>
                <th class="p-3">Program Outcome Description</th>
                <th class="p-3 text-right">Direct (80%)</th>
                <th class="p-3 text-right">Indirect (20%)</th>
                <th class="p-3 text-right font-bold text-white">Final Attainment (3.0)</th>
                <th class="p-3 text-right">Percentage</th>
                <th class="p-3 text-center">Target Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300">
              <tr v-for="po in attainment?.po_attainments" :key="po.po_code" class="hover:bg-slate-800/30">
                <td class="p-3 font-bold font-mono text-indigo-400 whitespace-nowrap">{{ po.po_code }}</td>
                <td class="p-3 text-slate-300 max-w-sm">{{ po.title }}</td>
                <td class="p-3 text-right font-mono">{{ po.direct_attainment }}</td>
                <td class="p-3 text-right font-mono text-slate-400">{{ po.indirect_attainment }}</td>
                <td class="p-3 text-right font-mono font-bold text-base text-emerald-400">{{ po.final_attainment }}</td>
                <td class="p-3 text-right font-semibold">{{ po.percentage }}%</td>
                <td class="p-3 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                        :class="po.is_attained ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'">
                    {{ po.is_attained ? 'Target Met' : 'Action Required' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PSO Attainment Table -->
      <div class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="px-4 py-3 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between">
          <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider">
            Program Specific Outcomes (PSO1 to PSO2) Attainment
          </h4>
          <span class="text-xs text-indigo-400 font-semibold">Specialization Level</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-950/80 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
              <tr>
                <th class="p-3">PSO Code</th>
                <th class="p-3">Program Specific Outcome Title</th>
                <th class="p-3 text-right">Direct (80%)</th>
                <th class="p-3 text-right">Indirect (20%)</th>
                <th class="p-3 text-right font-bold text-white">Final Score (3.0)</th>
                <th class="p-3 text-right">Percentage</th>
                <th class="p-3 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-slate-300">
              <tr v-for="pso in attainment?.pso_attainments" :key="pso.pso_code" class="hover:bg-slate-800/30">
                <td class="p-3 font-bold font-mono text-purple-400">{{ pso.pso_code }}</td>
                <td class="p-3 text-slate-300">{{ pso.title }}</td>
                <td class="p-3 text-right font-mono">{{ pso.direct_attainment }}</td>
                <td class="p-3 text-right font-mono text-slate-400">{{ pso.indirect_attainment }}</td>
                <td class="p-3 text-right font-mono font-bold text-base text-purple-400">{{ pso.final_attainment }}</td>
                <td class="p-3 text-right font-semibold">{{ pso.percentage }}%</td>
                <td class="p-3 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    Target Met
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- CO Trend Tab -->
    <div v-if="activeTab === 'co_trend'" class="space-y-6">
      <div class="flex items-center space-x-4 glass-card p-4 rounded-xl border border-slate-800">
        <label class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Select Course:</label>
        <select v-model="trendCourseId" @change="fetchCoTrends" class="bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white focus:border-emerald-500 outline-none w-64">
           <option v-for="c in coursesList" :key="c.id" :value="c.id">{{ c.course_code }} - {{ c.course_name }}</option>
        </select>
      </div>

      <div v-if="loadingTrends" class="flex items-center justify-center py-12">
        <div class="w-8 h-8 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
        <span class="ml-3 text-slate-400 text-sm font-semibold">Loading trends...</span>
      </div>
      
      <div v-else-if="!trendData || !trendData.cos || trendData.cos.length === 0" class="glass-card rounded-xl border border-slate-800 p-12 text-center">
        <p class="text-slate-400 text-sm">No historical attainment data available for this course.</p>
      </div>
      
      <div v-else class="glass-card rounded-xl border border-slate-800 p-6">
        <h4 class="text-lg font-bold text-white mb-2">CO-wise Year-on-Year Trend</h4>
        <p class="text-xs text-slate-400 mb-6">Attainment percentage values plotted across multiple academic years. Change indicators show progress from the previous available year.</p>
        
        <div class="h-[400px] w-full mb-8">
          <canvas ref="trendChart"></canvas>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-6 gap-4 border-t border-slate-800/80 pt-6">
          <div v-for="co in trendData.cos" :key="co.id" class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 text-center shadow-inner">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wide">{{ co.code }}</div>
            <div class="mt-1.5 font-mono text-base font-bold" :class="getChangeColorClass(co.percentage_change)">
              <span v-if="co.percentage_change !== null">{{ co.percentage_change > 0 ? '+' : '' }}{{ co.percentage_change }}%</span>
              <span v-else class="text-slate-600 text-xs">N/A</span>
            </div>
            <div class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider">vs Previous</div>
          </div>
        </div>
      </div>
    </div>

    <!-- CIE Heatmap Tab -->
    <div v-if="activeTab === 'cie_heatmap'" class="space-y-6">
      <div class="flex items-center space-x-4 glass-card p-4 rounded-xl border border-slate-800">
        <label class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Select Course:</label>
        <select v-model="heatmapCourseId" @change="fetchCieHeatmap" class="bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white focus:border-emerald-500 outline-none w-64">
           <option v-for="c in coursesList" :key="c.id" :value="c.id">{{ c.course_code }} - {{ c.course_name }}</option>
        </select>
      </div>

      <div v-if="loadingHeatmap" class="flex items-center justify-center py-12">
        <div class="w-8 h-8 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
        <span class="ml-3 text-slate-400 text-sm font-semibold">Loading heatmap...</span>
      </div>
      
      <div v-else-if="!heatmapData || !heatmapData.questions || heatmapData.questions.length === 0" class="glass-card rounded-xl border border-slate-800 p-12 text-center">
        <p class="text-slate-400 text-sm">No CIE assessment data available for this course.</p>
      </div>
      
      <div v-else class="glass-card rounded-xl border border-slate-800 p-6 overflow-x-auto">
        <h4 class="text-lg font-bold text-white mb-2">{{ heatmapData.batch_name }} ({{ heatmapData.assessment_type }})</h4>
        <p class="text-xs text-slate-400 mb-6">Question-wise performance heatmap. Green: &ge; 60%, Yellow: 40-59%, Red: &lt; 40%.</p>
        
        <table class="w-full text-left text-xs min-w-max">
          <thead class="bg-slate-950/80 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
            <tr>
              <th class="p-3 sticky left-0 bg-slate-950 z-10 w-32 border-r border-slate-800">Student ID</th>
              <th v-for="q in heatmapData.questions" :key="q.id" class="p-3 text-center border-l border-slate-800/50">
                <div class="text-slate-200">Q{{ q.question_number }}</div>
                <div class="text-[9px] text-indigo-400 mt-0.5">{{ q.co_code }}</div>
                <div class="text-[9px] text-slate-500 font-normal mt-0.5">Max: {{ q.max_marks }}</div>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60 text-slate-300">
            <tr v-for="student in heatmapData.students" :key="student.student_identifier" class="hover:bg-slate-800/30">
              <td class="p-3 font-mono font-bold sticky left-0 bg-slate-900 border-r border-slate-800 text-slate-300 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.5)]">
                {{ student.student_identifier }}
              </td>
              <td v-for="q in heatmapData.questions" :key="q.id" class="p-2 text-center border-l border-slate-800/50">
                <div v-if="student.marks[q.id] !== undefined" 
                     class="w-full py-1.5 rounded text-[11px] font-mono font-bold"
                     :class="getHeatmapColor(student.marks[q.id], q.max_marks)">
                  {{ student.marks[q.id] }}
                </div>
                <div v-else class="text-slate-600 text-[10px]">N/A</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Alerts Tab -->
    <div v-if="activeTab === 'alerts'" class="space-y-6">
      <div v-if="loadingAlerts" class="flex items-center justify-center py-12">
        <div class="w-8 h-8 border-4 border-rose-500/20 border-t-rose-500 rounded-full animate-spin"></div>
        <span class="ml-3 text-slate-400 text-sm font-semibold">Scanning for alerts...</span>
      </div>
      
      <div v-else-if="!alertsData || alertsData.length === 0" class="glass-card rounded-xl border border-slate-800 p-12 text-center">
        <div class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h4 class="text-lg font-bold text-white mb-2">No Low-Attainment Alerts</h4>
        <p class="text-slate-400 text-sm">All evaluated Course Outcomes meet or exceed their target thresholds.</p>
      </div>

      <div v-else class="space-y-4">
        <div class="px-4 py-3 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-start gap-3">
          <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          <div>
            <h4 class="text-sm font-bold text-rose-300">Action Required: Low Attainment Detected</h4>
            <p class="text-xs text-rose-400/80 mt-1">The following Course Outcomes have fallen below their configured target thresholds. Review curriculum delivery and assessment methods.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="(alert, i) in alertsData" :key="i" class="glass-card bg-slate-900 border border-rose-500/30 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-16 h-16 bg-rose-500/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            
            <div class="flex items-start justify-between relative z-10">
              <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] font-bold border border-slate-700">
                {{ alert.course_code }}
              </div>
              <span class="text-[10px] text-slate-500">{{ alert.academic_year }}</span>
            </div>
            
            <div class="mt-3 relative z-10">
              <h5 class="text-lg font-bold text-rose-400 font-mono">{{ alert.co_code }}</h5>
              <p class="text-xs text-slate-400 mt-1 line-clamp-2" :title="alert.co_name">{{ alert.co_name }}</p>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-between relative z-10">
              <div>
                <div class="text-[9px] text-slate-500 uppercase tracking-wider font-semibold">Attainment</div>
                <div class="text-sm font-bold text-white">{{ alert.attainment_value }} <span class="text-xs font-normal text-slate-500">/ 3.0</span></div>
              </div>
              <div class="text-right">
                <div class="text-[9px] text-slate-500 uppercase tracking-wider font-semibold">Target Threshold</div>
                <div class="text-sm font-bold text-emerald-400">{{ alert.threshold }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Record Marks Modal -->
    <div v-if="showMarksModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
      <div class="glass-card bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h4 class="text-sm font-bold text-white">Record Assessment Marks (Faculty Entry)</h4>
          <button @click="showMarksModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form @submit.prevent="submitMarks" class="space-y-3 text-xs">
          <div>
            <label class="font-semibold text-slate-300">Select Course</label>
            <select v-model="marksForm.course_id" @change="onCourseSelect" required
                    class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none">
              <option v-for="c in coursesList" :key="c.id" :value="c.id">
                {{ c.course_code }} - {{ c.course_name }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-semibold text-slate-300">Course Outcome (CO)</label>
              <select v-model="marksForm.course_outcome_id" required
                      class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none">
                <option v-for="co in activeCourseCos" :key="co.id" :value="co.id">
                  {{ co.co_code }} (Target: {{ co.target_level }})
                </option>
              </select>
            </div>
            <div>
              <label class="font-semibold text-slate-300">Assessment Type</label>
              <select v-model="marksForm.assessment_type" required
                      class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none">
                <option value="Internal_Test_1">Internal Test 1 (CIE)</option>
                <option value="Internal_Test_2">Internal Test 2 (CIE)</option>
                <option value="Assignment">Assignment / Quiz</option>
                <option value="End_Sem">End Semester Exam (SEE)</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-semibold text-slate-300">Total Students Attempted</label>
              <input type="number" v-model.number="marksForm.total_students_attempted" required min="1"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
            <div>
              <label class="font-semibold text-emerald-400">Students Scoring ≥ 60%</label>
              <input type="number" v-model.number="marksForm.students_meeting_target" required min="0"
                     class="w-full mt-1 bg-slate-950 border border-emerald-500/50 rounded-lg px-3 py-2 text-white focus:border-emerald-400 outline-none" />
            </div>
          </div>

          <!-- Live Calculated Attainment Level Preview -->
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs">
            <div>
              <span class="text-slate-400">Achieved Rate:</span>
              <strong class="ml-1 text-white font-mono">{{ livePercentage }}%</strong>
            </div>
            <div>
              <span class="text-slate-400">Attainment Level:</span>
              <strong class="ml-1 text-emerald-400 font-mono text-sm">Level {{ liveLevel }} / 3.0</strong>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-800">
            <button type="button" @click="showMarksModal = false" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300">Cancel</button>
            <button type="submit" :disabled="submitting" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold">
              {{ submitting ? 'Recording...' : 'Save Assessment Log' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import AppIcon from './AppIcon.vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

export default {
  name: 'AttainmentDashboard',
  components: { AppIcon },
  props: {
    attainment: {
      type: Object,
      default: () => ({}),
    },
    programId: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      activeTab: 'po_pso',
      showMarksModal: false,
      submitting: false,
      successMsg: '',
      coursesList: [],
      activeCourseCos: [],
      marksForm: {
        course_id: null,
        course_outcome_id: null,
        assessment_type: 'Internal_Test_1',
        total_students_attempted: 120,
        students_meeting_target: 95,
      },
      
      trendCourseId: null,
      trendData: null,
      loadingTrends: false,
      chartInstance: null,

      heatmapCourseId: null,
      heatmapData: null,
      loadingHeatmap: false,

      alertsData: null,
      loadingAlerts: false,
    };
  },
  computed: {
    livePercentage() {
      const total = Math.max(1, this.marksForm.total_students_attempted || 1);
      const meeting = Math.min(total, this.marksForm.students_meeting_target || 0);
      return ((meeting / total) * 100).toFixed(1);
    },
    liveLevel() {
      const p = parseFloat(this.livePercentage);
      if (p >= 70.0) return '3.0';
      if (p >= 60.0) return '2.0';
      if (p >= 50.0) return '1.0';
      return '0.5';
    },
  },
  mounted() {
    this.fetchCourses();
    this.fetchAlerts();
  },
  beforeUnmount() {
    if (this.chartInstance) {
      this.chartInstance.destroy();
    }
  },
  methods: {
    async fetchCourses() {
      try {
        const res = await axios.get('/api/v1/courses', {
          params: { program_id: this.programId },
        });
        if (res.data && res.data.success) {
          this.coursesList = res.data.data.courses || [];
          if (this.coursesList.length > 0) {
            this.marksForm.course_id = this.coursesList[0].id;
            this.trendCourseId = this.coursesList[0].id;
            this.heatmapCourseId = this.coursesList[0].id;
            this.onCourseSelect();
            if (this.activeTab === 'co_trend') {
              this.fetchCoTrends();
            } else if (this.activeTab === 'cie_heatmap') {
              this.fetchCieHeatmap();
            }
          }
        }
      } catch (err) {
        console.error('Failed to load courses:', err);
      }
    },
    onCourseSelect() {
      const c = this.coursesList.find((item) => item.id === this.marksForm.course_id);
      if (c && c.course_outcomes && c.course_outcomes.length > 0) {
        this.activeCourseCos = c.course_outcomes;
        this.marksForm.course_outcome_id = c.course_outcomes[0].id;
      } else {
        this.activeCourseCos = [];
      }
    },
    async submitMarks() {
      this.submitting = true;
      try {
        const res = await axios.post('/api/v1/attainment/record-marks', this.marksForm);
        if (res.data && res.data.success) {
          this.successMsg = res.data.message;
          this.showMarksModal = false;
          this.$emit('updated');
        }
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to record marks');
      } finally {
        this.submitting = false;
      }
    },
    
    // CO Trend Methods
    async fetchCoTrends() {
      if (!this.trendCourseId) return;
      this.loadingTrends = true;
      try {
        const res = await axios.get(`/api/v1/attainment/co-trends?course_id=${this.trendCourseId}`);
        if (res.data && res.data.success) {
          this.trendData = res.data.data;
          this.$nextTick(() => {
            this.renderChart();
          });
        }
      } catch (err) {
        console.error('Failed to load CO trends:', err);
      } finally {
        this.loadingTrends = false;
      }
    },
    renderChart() {
      if (this.chartInstance) {
        this.chartInstance.destroy();
      }
      
      if (!this.trendData || !this.trendData.cos || this.trendData.cos.length === 0 || !this.$refs.trendChart) {
        return;
      }
      
      const years = this.trendData.years;
      const cos = this.trendData.cos;
      
      const labels = cos.map(co => co.code);
      
      // Chart colors for different years
      const colors = [
        { bg: 'rgba(52, 211, 153, 0.8)', border: 'rgb(16, 185, 129)' }, // Emerald
        { bg: 'rgba(96, 165, 250, 0.8)', border: 'rgb(59, 130, 246)' }, // Blue
        { bg: 'rgba(167, 139, 250, 0.8)', border: 'rgb(139, 92, 246)' }  // Purple
      ];
      
      const datasets = years.map((year, index) => {
        const color = colors[index % colors.length];
        return {
          label: `${year.name} (${year.label})`,
          data: cos.map(co => (co.values[year.label] !== undefined && co.values[year.label] !== null) ? co.values[year.label] : null),
          backgroundColor: color.bg,
          borderColor: color.border,
          borderWidth: 1
        };
      });
      
      const ctx = this.$refs.trendChart.getContext('2d');
      this.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 3,
              ticks: {
                color: 'rgba(148, 163, 184, 0.8)'
              },
              grid: {
                color: 'rgba(30, 41, 59, 0.5)'
              }
            },
            x: {
              ticks: {
                color: 'rgba(148, 163, 184, 0.8)'
              },
              grid: {
                display: false
              }
            }
          },
          plugins: {
            legend: {
              labels: {
                color: 'rgba(203, 213, 225, 0.9)'
              }
            },
            tooltip: {
              mode: 'index',
              intersect: false
            }
          }
        }
      });
    },
    getChangeColorClass(change) {
      if (change === null) return 'text-slate-500';
      if (change > 0) return 'text-emerald-400';
      if (change < 0) return 'text-rose-400';
      return 'text-slate-300';
    },

    // Heatmap Methods
    async fetchCieHeatmap() {
      if (!this.heatmapCourseId) return;
      this.loadingHeatmap = true;
      try {
        const res = await axios.get(`/api/v1/attainment/cie-heatmap?course_id=${this.heatmapCourseId}`);
        if (res.data && res.data.success) {
          this.heatmapData = res.data.data;
        }
      } catch (err) {
        console.error('Failed to load CIE heatmap:', err);
      } finally {
        this.loadingHeatmap = false;
      }
    },
    getHeatmapColor(marks, max) {
      if (max <= 0) return 'bg-slate-800 text-slate-400';
      const percentage = (marks / max) * 100;
      if (percentage >= 60) return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
      if (percentage >= 40) return 'bg-amber-500/20 text-amber-400 border border-amber-500/30';
      return 'bg-rose-500/20 text-rose-400 border border-rose-500/30';
    },

    // Alerts Methods
    async fetchAlerts() {
      this.loadingAlerts = true;
      try {
        const res = await axios.get(`/api/v1/attainment/alerts?program_id=${this.programId}`);
        if (res.data && res.data.success) {
          this.alertsData = res.data.data;
        }
      } catch (err) {
        console.error('Failed to load alerts:', err);
      } finally {
        this.loadingAlerts = false;
      }
    }
  },
  watch: {
    activeTab(newTab) {
      if (newTab === 'co_trend' && !this.trendData && this.trendCourseId) {
        this.fetchCoTrends();
      } else if (newTab === 'co_trend' && this.trendData) {
        // Re-render chart when switching back to tab
        this.$nextTick(() => {
          this.renderChart();
        });
      } else if (newTab === 'cie_heatmap' && !this.heatmapData && this.heatmapCourseId) {
        this.fetchCieHeatmap();
      } else if (newTab === 'alerts' && !this.alertsData) {
        this.fetchAlerts();
      }
    }
  }
};
</script>
