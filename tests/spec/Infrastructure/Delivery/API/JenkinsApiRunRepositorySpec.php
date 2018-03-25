<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\Delivery\API;

use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Step\StepUri;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class JenkinsApiRunRepositorySpec extends ObjectBehavior
{

    function let(ClientInterface $client) {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JenkinsApiRunRepository::class);
    }

    function it_list_runs_by_iterating_pages(
        $client,
        ResponseInterface $firstPageResponse,
        ResponseInterface $secondPageResponse,
        ResponseInterface $thirdPageResponse,
        StreamInterface $firstPageStream,
        StreamInterface $secondPageStream,
        StreamInterface $thirdPageStream
    ) {
        $client->request('GET', 'pim-community-dev/runs/?start=0')->willReturn($firstPageResponse);
        $client->request('GET', 'pim-community-dev/runs/?start=1')->willReturn($secondPageResponse);
        $client->request('GET', 'pim-community-dev/runs/?start=2')->willReturn($thirdPageResponse);
        $firstPageResponse->getBody()->willReturn($firstPageStream);
        $secondPageResponse->getBody()->willReturn($secondPageStream);
        $thirdPageResponse->getBody()->willReturn($thirdPageStream);
        $firstPageStream->getContents()->willReturn($this->firstPage());
        $secondPageStream->getContents()->willReturn($this->secondPage());
        $thirdPageStream->getContents()->willReturn('[]');

        $run1 = new Run(
            'PR-7845',
            2,
            new PipelineName('pim-community-dev'),
            'UNKNOWN',
            'PAUSED',
            0,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.639+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.660+0000'),
            null,
            -1,
            -1,
            -1,
            -1,
            new StepUri('/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/steps/')
        );

        $run2 = new Run(
            '2.2',
            28,
            new PipelineName('pim-community-dev'),
            'SUCCESS',
            'FINISHED',
            14046469,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T03:20:01.921+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T03:20:02.030+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T07:14:08.499+0000'),
            0,
            0,
            8162,
            8162,
            new StepUri('/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/steps/')
        );

        $run3 = new Run(
            'PR-7844',
            3,
            new PipelineName('pim-community-dev'),
            'UNKNOWN',
            'RUNNING',
            0,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:06:20.160+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:06:20.179+0000'),
            null,
            2,
            0,
            6616,
            6618,
            new StepUri('/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/steps/')
        );

        $this->listRunsFrom(new PipelineName('pim-community-dev'))->shouldBeLike([$run1, $run2, $run3]);
    }

    private function firstPage(): string
    {
        return <<<'JSON'
            [
              {
                "_links" : {
                  "parent" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/"
                  },
                  "tests" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/tests/"
                  },
                  "nodes" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/nodes/"
                  },
                  "log" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/log/"
                  },
                  "self" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/"
                  },
                  "actions" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/actions/"
                  },
                  "steps" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/steps/"
                  },
                  "artifacts" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/artifacts/"
                  }
                },
                "actions" : [
                  
                ],
                "artifactsZipFile" : "/job/akeneo/job/pim-community-dev/job/PR-7845/2/artifact/*zip*/archive.zip",
                "causeOfBlockage" : null,
                "causes" : [
                  {
                    "_class" : "io.jenkins.blueocean.service.embedded.rest.AbstractRunImpl$BlueCauseImpl",
                    "shortDescription" : "Pull request #7845 updated"
                  }
                ],
                "changeSet" : [
                  {
                    "_class" : "io.jenkins.blueocean.service.embedded.rest.ChangeSetResource",
                    "_links" : {
                      "self" : {
                        "_class" : "io.jenkins.blueocean.rest.hal.Link",
                        "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/changeset/c78b581f967cb0e93e048e8346d0dfd64cbe7c0c/"
                      }
                    },
                    "affectedPaths" : [
                      "CHANGELOG-2.1.md"
                    ],
                    "author" : {
                      "_class" : "io.jenkins.blueocean.service.embedded.rest.UserImpl",
                      "_links" : {
                        "favorites" : {
                          "_class" : "io.jenkins.blueocean.rest.hal.Link",
                          "href" : "/blue/rest/users/noreply/favorites/"
                        },
                        "self" : {
                          "_class" : "io.jenkins.blueocean.rest.hal.Link",
                          "href" : "/blue/rest/users/noreply/"
                        }
                      },
                      "avatar" : null,
                      "email" : "noreply@github.com",
                      "fullName" : "noreply",
                      "id" : "noreply",
                      "permission" : null
                    },
                    "commitId" : "c78b581f967cb0e93e048e8346d0dfd64cbe7c0c",
                    "issues" : [
                      
                    ],
                    "msg" : "Update CHANGELOG-2.1.md",
                    "timestamp" : "2018-03-21T11:15:13.000+0000",
                    "url" : "https://github.com/akeneo/pim-community-dev/commit/c78b581f967cb0e93e048e8346d0dfd64cbe7c0c"
                  }
                ],
                "description" : null,
                "durationInMillis" : 0,
                "enQueueTime" : "2018-03-21T11:15:21.639+0000",
                "endTime" : null,
                "estimatedDurationInMillis" : -1,
                "id" : "2",
                "name" : null,
                "organization" : "jenkins",
                "pipeline" : "PR-7845",
                "replayable" : false,
                "result" : "UNKNOWN",
                "runSummary" : "?",
                "startTime" : "2018-03-21T11:15:21.660+0000",
                "state" : "PAUSED",
                "type" : "WorkflowRun",
                "branch" : {
                  "isPrimary" : false,
                  "issues" : [
                    
                  ],
                  "url" : "https://github.com/akeneo/pim-community-dev/pull/7845"
                },
                "commitId" : "c78b581f967cb0e93e048e8346d0dfd64cbe7c0c+25db2fe4f8cf39b25d2b48842f3779c86c490fb5",
                "commitUrl" : null,
                "pullRequest" : {
                  "author" : "KarenMayo",
                  "id" : "7845",
                  "title" : "2.1.6, 2.1.7 and 2.1.X added",
                  "url" : "https://github.com/akeneo/pim-community-dev/pull/7845"
                }
              },
              {
                 "_class" : "io.jenkins.blueocean.rest.impl.pipeline.PipelineRunImpl",
                 "_links" : {
                   "parent" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/"
                   },
                   "tests" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/tests/"
                   },
                   "nodes" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/nodes/"
                   },
                   "log" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/log/"
                   },
                   "self" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/"
                   },
                   "actions" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/actions/"
                   },
                   "steps" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/steps/"
                   },
                   "artifacts" : {
                     "_class" : "io.jenkins.blueocean.rest.hal.Link",
                     "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/artifacts/"
                   }
                 },
                 "actions" : [
                   
                 ],
                 "artifactsZipFile" : "/job/akeneo/job/pim-community-dev/job/2.2/28/artifact/*zip*/archive.zip",
                 "causeOfBlockage" : null,
                 "causes" : [
                   {
                     "_class" : "io.jenkins.blueocean.service.embedded.rest.AbstractRunImpl$BlueCauseImpl",
                     "shortDescription" : "Started by user akeneo-ci",
                     "userId" : "akeneo-ci",
                     "userName" : "akeneo-ci"
                   }
                 ],
                 "changeSet" : [
                   {
                     "_class" : "io.jenkins.blueocean.service.embedded.rest.ChangeSetResource",
                     "_links" : {
                       "self" : {
                         "_class" : "io.jenkins.blueocean.rest.hal.Link",
                         "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/changeset/1a68bdc82c4e2e0a99d37509abfd30afd9615b36/"
                       }
                     },
                     "affectedPaths" : [
                       "upgrades/schema/Version_2_2_20180320093100_remove_product_type.php"
                     ],
                     "author" : {
                       "_class" : "io.jenkins.blueocean.service.embedded.rest.UserImpl",
                       "_links" : {
                         "favorites" : {
                           "_class" : "io.jenkins.blueocean.rest.hal.Link",
                           "href" : "/blue/rest/users/samir.boulil/favorites/"
                         },
                         "self" : {
                           "_class" : "io.jenkins.blueocean.rest.hal.Link",
                           "href" : "/blue/rest/users/samir.boulil/"
                         }
                       },
                       "avatar" : null,
                       "email" : "samir.boulil@akeneo.com",
                       "fullName" : "samir.boulil",
                       "id" : "samir.boulil",
                       "permission" : null
                     },
                     "commitId" : "1a68bdc82c4e2e0a99d37509abfd30afd9615b36",
                     "issues" : [
                       
                     ],
                     "msg" : "Migration: Add migration to remove the product type from db",
                     "timestamp" : "2018-03-20T13:38:47.000+0000",
                     "url" : "https://github.com/akeneo/pim-community-dev/commit/1a68bdc82c4e2e0a99d37509abfd30afd9615b36"
                   }
                 ],
                 "description" : null,
                 "durationInMillis" : 14046469,
                 "enQueueTime" : "2018-03-21T03:20:01.921+0000",
                 "endTime" : "2018-03-21T07:14:08.499+0000",
                 "estimatedDurationInMillis" : 11296869,
                 "id" : "28",
                 "name" : null,
                 "organization" : "jenkins",
                 "pipeline" : "2.2",
                 "replayable" : true,
                 "result" : "SUCCESS",
                 "runSummary" : "stable",
                 "startTime" : "2018-03-21T03:20:02.030+0000",
                 "state" : "FINISHED",
                 "testSummary" : {
                   "existingFailed" : 0,
                   "failed" : 0,
                   "fixed" : 0,
                   "passed" : 8162,
                   "regressions" : 0,
                   "skipped" : 0,
                   "total" : 8162
                 },
                 "type" : "WorkflowRun",
                 "branch" : {
                   "isPrimary" : true,
                   "issues" : [
                     
                   ],
                   "url" : "https://github.com/akeneo/pim-community-dev/tree/2.2"
                 },
                 "commitId" : "3ce0d861cdef18492941d2d2dc81b60f81e1e603",
                 "commitUrl" : null,
                 "pullRequest" : null
               }
            ]
JSON;
    }

    private function secondPage(): string
    {
        return <<<'JSON'
            [
              {
                "_class" : "io.jenkins.blueocean.rest.impl.pipeline.PipelineRunImpl",
                "_links" : {
                  "parent" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/"
                  },
                  "tests" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/tests/"
                  },
                  "nodes" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/nodes/"
                  },
                  "log" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/log/"
                  },
                  "self" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/"
                  },
                  "actions" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/actions/"
                  },
                  "steps" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/steps/"
                  },
                  "artifacts" : {
                    "_class" : "io.jenkins.blueocean.rest.hal.Link",
                    "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/artifacts/"
                  }
                },
                "actions" : [
                  
                ],
                "artifactsZipFile" : "/job/akeneo/job/pim-community-dev/job/PR-7844/3/artifact/*zip*/archive.zip",
                "causeOfBlockage" : null,
                "causes" : [
                  {
                    "_class" : "io.jenkins.blueocean.service.embedded.rest.AbstractRunImpl$BlueCauseImpl",
                    "shortDescription" : "Pull request #7844 updated"
                  }
                ],
                "changeSet" : [
                  {
                    "_class" : "io.jenkins.blueocean.service.embedded.rest.ChangeSetResource",
                    "_links" : {
                      "self" : {
                        "_class" : "io.jenkins.blueocean.rest.hal.Link",
                        "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7844/runs/3/changeset/14418da0f7a40abd4b4df168755e0274abd1e2d9/"
                      }
                    },
                    "affectedPaths" : [
                      "CHANGELOG-2.0.md"
                    ],
                    "author" : {
                      "_class" : "io.jenkins.blueocean.service.embedded.rest.UserImpl",
                      "_links" : {
                        "favorites" : {
                          "_class" : "io.jenkins.blueocean.rest.hal.Link",
                          "href" : "/blue/rest/users/grena/favorites/"
                        },
                        "self" : {
                          "_class" : "io.jenkins.blueocean.rest.hal.Link",
                          "href" : "/blue/rest/users/grena/"
                        }
                      },
                      "avatar" : null,
                      "email" : "hello@grena.fr",
                      "fullName" : "Adrien Pétremann",
                      "id" : "grena",
                      "permission" : null
                    },
                    "commitId" : "14418da0f7a40abd4b4df168755e0274abd1e2d9",
                    "issues" : [
                      
                    ],
                    "msg" : "Update changelog to add AOB-63",
                    "timestamp" : "2018-03-21T10:02:56.000+0000",
                    "url" : "https://github.com/akeneo/pim-community-dev/commit/14418da0f7a40abd4b4df168755e0274abd1e2d9"
                  }
                ],
                "description" : null,
                "durationInMillis" : 0,
                "enQueueTime" : "2018-03-21T11:06:20.160+0000",
                "endTime" : null,
                "estimatedDurationInMillis" : 12729292,
                "id" : "3",
                "name" : null,
                "organization" : "jenkins",
                "pipeline" : "PR-7844",
                "replayable" : true,
                "result" : "UNKNOWN",
                "runSummary" : "?",
                "startTime" : "2018-03-21T11:06:20.179+0000",
                "state" : "RUNNING",
                "testSummary" : {
                  "existingFailed" : 2,
                  "failed" : 2,
                  "fixed" : 185,
                  "passed" : 6616,
                  "regressions" : 0,
                  "skipped" : 0,
                  "total" : 6618
                },
                "type" : "WorkflowRun",
                "branch" : {
                  "isPrimary" : false,
                  "issues" : [
                    
                  ],
                  "url" : "https://github.com/akeneo/pim-community-dev/pull/7844"
                },
                "commitId" : "46c5113eed63ce5ab20b9202e4cc22f5f8cc9223+56ee9e885faeb70d4feabfb7b04d40a3d20fe1a5",
                "commitUrl" : null,
                "pullRequest" : {
                  "author" : "anaelChardan",
                  "id" : "7844",
                  "title" : "Reduce slowness due to ES pre-checks",
                  "url" : "https://github.com/akeneo/pim-community-dev/pull/7844"
                }
              }
            ]
JSON;
    }
}
