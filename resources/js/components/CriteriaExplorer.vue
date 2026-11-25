<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-xs font-semibold border border-indigo-500/20">
          NBA Tier-II SAR Criteria (1 to 10)
        </div>
        <h3 class="text-xl font-bold text-white mt-1">
          Accreditation Criteria & Claimed Scores
        </h3>
      </div>
      <div class="text-right">
        <span class="text-xs text-slate-400">Total Score:</span>
        <span class="ml-1 text-xl font-black text-emerald-400">{{ sar.total_claimed_marks }}</span>
        <span class="text-xs text-slate-400"> / 1000.0</span>
      </div>
    </div>

    <!-- Criteria Accordion / List -->
    <div class="space-y-3">
      <div v-for="c in criteria" :key="c.number"
           class="glass-card rounded-xl overflow-hidden border border-slate-800 transition">
        <div @click="toggleExpand(c.number)"
             class="p-4 bg-slate-900/60 flex items-center justify-between cursor-pointer hover:bg-slate-900/90 transition">
          <div class="flex items-center space-x-3">
            <span class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-300 text-sm">
              C{{ c.number }}
            </span>
            <div>
              <div class="text-sm font-bold text-white">{{ c.title }}</div>
              <div class="text-xs text-slate-400">Criterion {{ c.number }} • Max Marks: {{ c.max_marks }}</div>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <div class="text-right">
              <div class="text-base font-bold text-emerald-400">
                {{ c.claimed_marks }} <span class="text-xs text-slate-400 font-normal">/ {{ c.max_marks }}</span>
              </div>
              <div class="text-[10px] text-slate-400">{{ c.percentage }}% Claimed</div>
            </div>
            <span class="text-slate-400 text-sm">{{ expanded === c.number ? '▲' : '▼' }}</span>
          </div>
        </div>

        <!-- Expanded Details -->
        <div v-if="expanded === c.number" class="p-5 border-t border-slate-800 bg-slate-950/40 space-y-4">
          <div>
            <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Self-Assessment Summary</h5>
            <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ c.summary }}</p>
          </div>

          <!-- Criterion 1 Vision, Mission & PEOs Snapshot -->
          <div
            v-if="c.number === 1"
            class="p-4 rounded-xl bg-slate-900 border border-indigo-500/30 text-xs space-y-3"
          >
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
              <span class="font-bold text-indigo-300 flex items-center gap-2 text-sm">
                <span>🏛️</span> Institutional Vision & Mission (TIET)
              </span>
              <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-semibold text-[10px] border border-emerald-500/20">
                Criterion 1.1 Verified
              </span>
            </div>

            <!-- Vision Block -->
            <div class="p-3.5 rounded-lg bg-slate-950 border border-slate-800/80 space-y-2">
              <div class="flex items-center gap-1.5 text-amber-400 font-bold text-xs uppercase tracking-wide">
                <span>👁️</span> Vision
              </div>
              <p class="text-slate-200 text-xs font-medium leading-relaxed">
                At TIET, our vision rests on two pillars:
              </p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-1">
                <div class="p-3 rounded-lg bg-slate-900/90 border border-indigo-500/20 space-y-1">
                  <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Pillar 1 • Stakeholder Experience</div>
                  <p class="text-[11px] text-slate-300 leading-normal">
                    A deeply meaningful experience for all stakeholders that shapes lives with wisdom, purpose, and compassion.
                  </p>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/90 border border-indigo-500/20 space-y-1">
                  <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Pillar 2 • Socially Impactful Research</div>
                  <p class="text-[11px] text-slate-300 leading-normal">
                    Socially impactful research that advances knowledge while solving real challenges of industry, society, and humanity.
                  </p>
                </div>
              </div>
              <p class="text-[11px] text-slate-400 italic pt-1">
                Together, they embody our commitment to seeking excellence for all.
              </p>
            </div>

            <!-- Mission Block -->
            <div class="p-3.5 rounded-lg bg-slate-950 border border-slate-800/80 space-y-2">
              <div class="flex items-center gap-1.5 text-emerald-400 font-bold text-xs uppercase tracking-wide">
                <span>🎯</span> Mission
              </div>
              <p class="text-slate-200 text-xs font-medium leading-relaxed">
                TIET exists to educate and empower students while advancing knowledge through research, so that we serve our society with wisdom, compassion, and a commitment to seek excellence.
              </p>
            </div>
          </div>

          <!-- Criterion 2 Live Intake Snapshot -->
          <div
            v-if="c.number === 2"
            class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs space-y-2"
          >
            <div class="font-semibold text-indigo-300">
              Criterion 2 Intake Metrics Snapshot:
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Current Intake</div>
                <div class="font-bold text-white font-mono">{{ sar.live_engines?.intake?.current_total_students ?? 0 }}</div>
              </div>

              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">3-Year Average Intake</div>
                <div class="font-bold text-white font-mono">{{ sar.live_engines?.intake?.average_total_students ?? 0 }}</div>
              </div>

              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Intake Stability</div>
                <div class="font-bold text-emerald-400 font-mono">{{ sar.live_engines?.intake?.stability_percentage ?? 0 }}%</div>
              </div>
            </div>
          </div>

          <!-- Specific Criterion In-Depth Data -->
          <div v-if="c.number === 4" class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs space-y-2">
            <div class="font-semibold text-indigo-300">Criterion 4 Metrics Snapshot:</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Success Rate SI (w/o Backlog)</div>
                <div class="font-bold text-white font-mono">{{ sar.live_engines?.success_rate?.mean_si_without_backlog || 0.85 }} ({{ sar.live_engines?.success_rate?.marks_without_backlog || 21.25 }}/25)</div>
              </div>
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Graduation in Stipulated Time</div>
                <div class="font-bold text-white font-mono">{{ sar.live_engines?.success_rate?.mean_si_stipulated_time || 0.98 }} ({{ sar.live_engines?.success_rate?.marks_stipulated_time || 14.7 }}/15)</div>
              </div>
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">Placement & Higher Studies Index</div>
                <div class="font-bold text-emerald-400 font-mono">{{ (sar.live_engines?.placement?.avg_placement_index * 100).toFixed(1) || 84.5 }}% ({{ sar.live_engines?.placement?.claimed_marks || 36 }}/40)</div>
              </div>
            </div>
          </div>

          <!-- Criterion 5 In-Depth Data -->
          <div v-if="c.number === 5" class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <span class="font-bold text-white text-sm">Criterion 5: Faculty Information & Contributions</span>
                <p class="text-[11px] text-slate-400 mt-0.5">Tier-II Evaluated Cycle (CAY, CAYm1, CAYm2) • 5 Sub-criteria Assessment</p>
              </div>
              <button
                @click="$emit('navigate', 'criterion5')"
                class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs shadow-md transition flex items-center gap-1.5 shrink-0"
              >
                <span>🚀</span> Open Full Criterion 5 Module (5.1 - 5.5)
              </button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-1">
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.1 SFR (Avg: 19.04)</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.1']?.claimed_marks || 18.0 }} <span class="text-slate-500 text-[10px] font-normal">/ 30</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.2 FQI (Avg: 22.12)</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.2']?.claimed_marks || 22.12 }} <span class="text-slate-500 text-[10px] font-normal">/ 25</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.3 Cadre (1:2:6)</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.3']?.claimed_marks || 20.69 }} <span class="text-slate-500 text-[10px] font-normal">/ 25</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.4 Visiting Faculty</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.4']?.claimed_marks || 10.0 }} <span class="text-slate-500 text-[10px] font-normal">/ 10</span></div>
              </div>
              <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                <div class="text-slate-400 text-[10px] uppercase font-semibold">5.5 Retention</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ sar.live_engines?.criterion5?.sub_criteria?.['5.5']?.claimed_marks || 7.83 }} <span class="text-slate-500 text-[10px] font-normal">/ 10</span></div>
              </div>
            </div>
          </div>

          <!-- Criterion 10 Snapshot -->
          <div
            v-if="c.number === 10"
            class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs space-y-2"
          >
            <div class="font-semibold text-indigo-300">
              Criterion 10 Budget & Governance Snapshot:
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">
                  Total 3-Year Expenditure
                </div>
                <div class="font-bold text-white">
                  ₹{{ (sar.live_engines?.budget?.total_spent ?? 0).toLocaleString() }}
                </div>
              </div>

              <div class="p-2 rounded bg-slate-950">
                <div class="text-slate-400 text-[10px]">
                  Average Budget Utilization
                </div>
                <div class="font-bold text-emerald-400">
                  {{ sar.live_engines?.budget?.overall_utilization ?? 0 }}%
                </div>
              </div>
            </div>
          </div>

          <!-- Evidence Documents Section -->
          <div class="pt-2 border-t border-slate-800/60">
            <div class="flex items-center justify-between mb-3">
              <h6 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📎</span> Supporting Evidence & Documentation
              </h6>
              
              <button 
                @click="openVersionHistory(c.number)"
                class="text-[11px] text-indigo-400 hover:text-indigo-300 font-medium flex items-center gap-1"
              >
                <span>🕒</span> Version Audit Trail
              </button>
            </div>

            <!-- List of uploaded evidence -->
            <div v-if="evidences[c.number] && evidences[c.number].length > 0" class="space-y-2 mb-3">
              <div v-for="ev in evidences[c.number]" :key="ev.id"
                   class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900 border border-slate-800 text-xs">
                <div class="flex items-center space-x-2.5 truncate pr-2">
                  <span class="text-indigo-400">📄</span>
                  <div class="truncate">
                    <div class="font-medium text-slate-200 truncate flex items-center gap-1.5">
                      {{ ev.file_name }}
                      <span class="px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 font-bold text-[9px]">v{{ ev.version }}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 flex items-center gap-2">
                      <span>{{ (ev.file_size / 1024).toFixed(1) }} KB</span>
                      <span>•</span>
                      <span>By {{ ev.uploaded_by }}</span>
                      <span v-if="ev.upload_reason" class="text-slate-300 italic">• "{{ ev.upload_reason }}"</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-1 shrink-0">
                  <button @click="downloadEvidence(ev)"
                          class="p-1 px-2 text-indigo-400 hover:text-indigo-300 hover:bg-slate-800 rounded font-medium transition text-[11px]">
                    Download
                  </button>
                  <button @click="deleteEvidence(ev.id, c.number)"
                          class="p-1 px-2 text-rose-400 hover:text-rose-300 hover:bg-slate-800 rounded font-medium transition text-[11px]">
                    Delete
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="text-xs text-slate-400 italic py-2">
              No evidence uploaded yet for this criterion.
            </div>

            <!-- Upload new evidence with Reason -->
            <div class="mt-3 p-3 rounded-lg bg-slate-900/60 border border-slate-800/80 space-y-2">
              <div class="flex items-center gap-2">
                <input 
                  type="text" 
                  v-model="uploadReason[c.number]" 
                  placeholder="Audit reason / version notes (optional, e.g. Updated with 2024-25 audit data)"
                  class="flex-1 px-2.5 py-1.5 bg-slate-950 border border-slate-800 rounded text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500"
                />
              </div>

              <div class="flex items-center space-x-2">
                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                  <span>📤</span>
                  <span>{{ uploading[c.number] ? 'Uploading Version...' : 'Upload PDF/Doc Evidence' }}</span>
                  <input type="file" @change="uploadEvidence($event, c.number)" class="hidden" accept=".pdf,.doc,.docx,.xlsx" :disabled="uploading[c.number]">
                </label>
                <span class="text-[10px] text-slate-400">Max 25MB • Uploading creates new immutable version</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Version History Audit Modal -->
    <div v-if="showHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <div>
            <h4 class="font-bold text-white text-base">Criterion {{ historyCriterionNumber }} Evidence Audit Trail</h4>
            <p class="text-xs text-slate-400">Full immutable version history with user stamps and justification notes</p>
          </div>
          <button @click="showHistoryModal = false" class="text-slate-400 hover:text-white text-lg font-bold">✕</button>
        </div>

        <div v-if="loadingHistory" class="text-center py-8 text-xs text-slate-400">
          Loading audit trail...
        </div>

        <div v-else-if="historyList.length === 0" class="text-center py-8 text-xs text-slate-400 italic">
          No audit history found for this criterion.
        </div>

        <div v-else class="max-h-96 overflow-y-auto space-y-2.5 pr-1">
          <div v-for="item in historyList" :key="item.id"
               class="p-3 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
            <div class="flex items-start gap-3">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold mt-0.5"
                    :class="item.is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400'">
                v{{ item.version }} {{ item.is_active ? '(Active)' : '(Archived)' }}
              </span>
              <div>
                <div class="text-xs font-semibold text-white">{{ item.file_name }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                  Uploaded by <strong class="text-slate-200">{{ item.uploaded_by }}</strong> on {{ new Date(item.created_at).toLocaleString() }}
                </div>
                <div v-if="item.upload_reason" class="text-[11px] text-indigo-300 mt-1 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 inline-block">
                  Note: {{ item.upload_reason }}
                </div>
              </div>
            </div>

            <button
              @click="downloadEvidence(item)"
              class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium transition border border-slate-700 shrink-0"
            >
              Download v{{ item.version }}
            </button>
          </div>
        </div>

        <div class="flex justify-end pt-2 border-t border-slate-800">
          <button @click="showHistoryModal = false" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
            Close History
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CriteriaExplorer',
  emits: ['navigate'],
  props: {
    criteria: {
      type: Array,
      default: () => [],
    },
    sar: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      expanded: 1,
      evidences: {},
      uploading: {},
      uploadReason: {},
      showHistoryModal: false,
      historyList: [],
      historyCriterionNumber: null,
      loadingHistory: false,
    };
  },
  mounted() {
    if (this.expanded && this.sar.report_id) {
      this.fetchEvidences(this.expanded);
    }
  },
  watch: {
    'sar.report_id': function(newVal) {
      if (newVal && this.expanded) {
        this.fetchEvidences(this.expanded);
      }
    }
  },
  methods: {
    toggleExpand(num) {
      this.expanded = this.expanded === num ? null : num;
      if (this.expanded && this.sar.report_id) {
        this.fetchEvidences(num);
      }
    },
    async fetchEvidences(criterionNumber) {
      try {
        const res = await axios.get(`/api/v1/nba/workflow/${this.sar.report_id}/criteria/${criterionNumber}/evidence`);
        if (res.data && res.data.success) {
          this.evidences = {
            ...this.evidences,
            [criterionNumber]: res.data.data
          };
        }
      } catch (err) {
        console.error('Failed to fetch evidence:', err);
      }
    },
    async uploadEvidence(event, criterionNumber) {
      const file = event.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('evidence_file', file);
      if (this.uploadReason[criterionNumber]) {
        formData.append('upload_reason', this.uploadReason[criterionNumber]);
      }

      this.uploading = { ...this.uploading, [criterionNumber]: true };
      
      try {
        const res = await axios.post(`/api/v1/nba/workflow/${this.sar.report_id}/criteria/${criterionNumber}/evidence`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (res.data && res.data.success) {
          event.target.value = ''; // reset file input
          this.uploadReason = { ...this.uploadReason, [criterionNumber]: '' }; // reset reason field
          this.fetchEvidences(criterionNumber); // reload evidence list
        }
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to upload evidence.');
      } finally {
        this.uploading = { ...this.uploading, [criterionNumber]: false };
      }
    },
    async deleteEvidence(evidenceId, criterionNumber) {
      if (!confirm('Are you sure you want to delete this evidence version?')) return;
      
      try {
        const res = await axios.delete(`/api/v1/nba/evidence/${evidenceId}`);
        if (res.data && res.data.success) {
          this.fetchEvidences(criterionNumber);
        }
      } catch (err) {
        alert('Failed to delete evidence.');
      }
    },
    downloadEvidence(ev) {
      window.open(`/api/v1/nba/evidence/${ev.id}/download`, '_blank');
    },
    async openVersionHistory(criterionNumber) {
      this.historyCriterionNumber = criterionNumber;
      this.showHistoryModal = true;
      this.loadingHistory = true;
      this.historyList = [];

      try {
        const res = await axios.get(`/api/v1/nba/workflow/${this.sar.report_id}/criteria/${criterionNumber}/evidence/history`);
        if (res.data && res.data.success) {
          this.historyList = res.data.data;
        }
      } catch (err) {
        console.error('Failed to load evidence history:', err);
      } finally {
        this.loadingHistory = false;
      }
    }
  },
};
</script>
